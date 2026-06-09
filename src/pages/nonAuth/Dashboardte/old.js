import React, { useState, useEffect } from 'react'
import { SafeAreaView, ScrollView, Text, TextInput, View, Image, TouchableOpacity, Modal, Platform } from 'react-native'
import DropDownPicker from 'react-native-dropdown-picker'
import styles from './DashBoardTeStyle'
import { getApi, postApiWithHeader, getApiWithHeader, getApiWithHeaderr } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import moment from 'moment'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

var user_details
var _editable_obj

const DashboardTe = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [openMnt, setOpenMnt] = useState(false)
    const [openYr, setOpenYr] = useState(false)
    const [liftingHistory, setLiftingHistory] = useState([])
    const [copyLiftingHistory, setCopyLiftingHistory] = useState([])
    const [modalVisible, setModalVisible] = useState(false)

    //For edit
    const [open, setOpen] = useState(false)
    const [value, setValue] = useState('')
    const [products, setProducts] = useState('')
    const [qty, setQty] = useState('')
    const [remark, setRemark] = useState('')
    const [error, setError] = useState(false)
    const [errorTxt, setErrorTxt] = useState('')
    const [isStartDate, setIsStartDate] = useState(false)
    const [isEndDate, setIsEndDate] = useState(false)
    const [startDate, setStartDate] = useState(textValue.start_date)
    const [endDate, setEndDate] = useState(textValue.end_date)
    const [dashboardDetails, setDashboardDetails] = useState('')

    const url1 = 'te/dashboard/mason?preferred_app_lang='+selectedLanguage()
    const url2 = 'te/dashboard/mason?status=1&preferred_app_lang='+selectedLanguage()
    const url3 = 'te/dashboard/lifting?is_verified=1&preferred_app_lang='+selectedLanguage()
    const url4 = 'te/dashboard/lifting?is_verified=0&preferred_app_lang='+selectedLanguage()
    const url5 = 'te/dashboard/liftingbags?product_id=1&preferred_app_lang='+selectedLanguage()
    const url6 = 'te/dashboard/liftingbags?product_id=2&preferred_app_lang='+selectedLanguage()
    const url7 = 'te/dashboard/mason/netpoint?preferred_app_lang='+selectedLanguage()
    const url8 = 'te/dashboard/status/gift?preferred_app_lang='+selectedLanguage()
    const url9 = 'te/dashboard/status/gift?status=0&preferred_app_lang='+selectedLanguage()
    const url10 = 'te/dashboard/status/gift?status=1&preferred_app_lang='+selectedLanguage()
    const url11 = 'te/dashboard/status/query?preferred_app_lang='+selectedLanguage()
    const url12 = 'te/dashboard/status/query?status=1&preferred_app_lang='+selectedLanguage()
    const url13 = 'te/dashboard/status/query?status=2&preferred_app_lang='+selectedLanguage()

    useEffect(() => {
        getTeDetails()
    }, [])

    const getTeDetails = async () => {
        if (startDate == textValue.start_date || endDate == textValue.end_date) {
            setLoading(true)
            getApiWithHeader(constants.te_dashboard+'?preferred_app_lang='+selectedLanguage())
                .then(response => {
                    if (response.data.status) {
                        setLoading(false)
                        setDashboardDetails(response.data.data)
                    }
                })
                .catch(err => {
                    
                    setLoading(false)
                    showToast('error', messageList.t4)
                })
        } else {
            setLoading(true)
            getApiWithHeaderr(constants.te_dashboard + `?from_date=${startDate}&to_date=${endDate}`+'&preferred_app_lang='+selectedLanguage())
                .then(response => {
                    if (response.data.status) {
                        setLoading(false)
                        setDashboardDetails(response.data.data)
                    }
                })
                .catch(err => {
                    
                    setLoading(false)
                    showToast('error', messageList.t4)
                })
        }
    }

    const getData = async () => {
        try {
            const value = await AsyncStorage.getItem('user_info')
            if (value !== null) {
                user_details = JSON.parse(value)
                setLoading(true)
                get_lifting_history()
            }
        } catch (e) { }
    }

    const get_lifting_history = async () => {
        var formData = new FormData()
        formData.append('id', user_details?.data?.id)
        formData.append('role', user_details?.data?.role)
        formData.append('preferred_app_lang', selectedLanguage())
        
        postApiWithHeader(constants.get_lifting_history, formData)
            .then(response => {
                // setLoading(false)
                
                if (response.data.status) {
                    let data = response.data.data
                    setLiftingHistory(data)
                    setCopyLiftingHistory(data)
                    get_products(1,[])
                }
                else {
                    setLoading(false)
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else {
                        showToast('error', response?.data?.msg)
                    }
                }
            })
            .catch(err => {
                
                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const showToast = (type, msg) => {
        Toast.show({
            type: type,
            text2: msg,
            text2NumberOfLines:2
        })
    }

    const form_validate = () => {
        if (value == null) {
            setError(true)
            setErrorTxt(messageList.t37)
        } else if (qty == '') {
            setError(true)
            setErrorTxt(messageList.t32)
        } else {
            setError(false)
            setErrorTxt('')
            setLoading(true)
            changeLanguage()
        }
    }
    const changeLanguage=()=>{
        setLoading(true)
        var arr = [remark]
        const myHeaders = new Headers()
        myHeaders.append('Content-Type', 'application/json')

        const raw = JSON.stringify({
            'q': arr,
            'target': 'en'
        })

        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: raw,
            redirect: 'follow'
        }

        fetch('https://translation.googleapis.com/language/translate/v2?key=AIzaSyBGLsoao9R0m9mEYxVrvNWnSu2ullebn2I', requestOptions)
            .then((response) => response.json())
            .then((result) => {
                update_lifting(result.data.translations[0].translatedText)
            })
            .catch(err => {
                update_lifting(arr[0])
                // setLoading(false)
                //                 Toast.show({
                //                     type: 'error',
                //                     text2: 'Sorry google translations not working',
                //                     text2NumberOfLines: 2
                //                 })
            })
    }

    const update_lifting = async (remark) => {
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('id', _editable_obj.lifting_id)
        formData.append('product_id', value)
        formData.append('qty', convertForUploadData(qty))
        formData.append('lifting_date', moment(new Date()).format('DD-MM-YYYY'))
        formData.append('remark', convertForUploadData(remark))
        formData.append('img', '')
        formData.append('preferred_app_lang', selectedLanguage())
        postApiWithHeader(constants.update_lifting, formData)
            .then(response => {
                setModalVisible(false)
                
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    get_lifting_history()
                } else {
                    showToast('error', response.data.msg)
                    getData()
                }
            })
            .catch(err => {
                
                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const get_products = async (page_value,value) => {
        getApi(constants.get_all_products+'?page='+page_value+'&preferred_app_lang='+selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    let data = response.data.data
                    let products = []
                    for (var i = 0; i < data.length; i++) {
                        let obj =
                        {
                            label: data[i].name,
                            value: data[i].id
                        }
                        products.push(obj)
                    }
                    var a = value
                    a = [...a, ...products]
                    setProducts(a)
                    get_products(page_value+1,a)
                } else {
                    if(page_value==1){ showToast('error', response.data.msg)}
                }
            })
            .catch(err => {
                
                setLoading(false)
                // showToast('error', messageList.t4)
            })
    }

    const _logout = async () => {
        try {
            const keys = ['user_info', 'access_token']
            await AsyncStorage.multiRemove(keys)
            props.navigation.reset({
                index: 0,
                routes: [{ name: 'AuthStack' }],
            })
        } catch (e) { }
    }

    const hideDatePickerStart = () => {
        setIsStartDate(false)
    }

    const handleConfirmStart = (date) => {
        setStartDate(moment(date).format('YYYY-MM-DD'))
        console.warn('A date has been picked: ', date)
        hideDatePickerStart()
    }

    const hideDatePickerEnd = () => {
        setIsEndDate(false)
    }

    const handleConfirmEnd = (date) => {
        setEndDate(moment(date).format('YYYY-MM-DD'))
        console.warn('A date has been picked: ', date)
        hideDatePickerEnd()
    }

    const getFilteredData = () => {
        if (startDate ==textValue.start_date|| endDate == textValue.end_date) {
            showToast('error', messageList.t38)
        } else {
            getTeDetails()
        }
    }

    const getDashBoardDetailsOther = (url, type) => {
        let urll = ''
        if (startDate !== textValue.start_date && endDate !== textValue.end_date) {
            urll = url + `&from_date=${startDate}&to_date=${endDate}`
        } else {
            urll = url
        }
        props.navigation.navigate('DashboardTeDetails', { category: type, url: urll })
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._bgColor}>
                <View style={styles._upperView}>
                    <TouchableOpacity style={styles._upperView._back_btn}
                        onPress={() => {
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}
                    >

                        <Image style={styles._upperView._back_btn._img} source={Icons.back} />
                    </TouchableOpacity>

                    <View style={{ justifyContent: 'center', alignItems: 'center' }}>
                        <Text style={[styles._upperView._txt, { marginBottom: user_details?.data?.role == 2 ? 0 : 20 }]}>{convertForShowData(textValue.DASHBOARD)}</Text>
                    </View>
                </View>

                <View style={styles._lowerView}>
                    <View style={{ justifyContent: 'center', alignItems: 'center', marginTop: 10, marginBottom: -8, zIndex: 999 }}>
                        <View style={[styles._dropdown_view, { height: Platform.OS == 'android' ? openMnt || openYr ? 178 : 35 : 35 }]}>
                            <TouchableOpacity onPress={() => setIsStartDate(true)} style={[styles._dropdown_view._left_view, { height: Platform.OS == 'android' ? openMnt ? 178 : 35 : 35 }]}>
                                <Text style={{ color: '#000000' }}>{convertForShowData(startDate)}</Text>
                            </TouchableOpacity>
                            <DateTimePickerModal
                                isVisible={isStartDate}
                                mode='date'
                                onConfirm={handleConfirmStart}
                                onCancel={hideDatePickerStart}
                                date={new Date()}
                            />
                            <TouchableOpacity onPress={() => setIsEndDate(true)} style={[styles._dropdown_view._right_view, { height: Platform.OS == 'android' ? openYr ? 178 : 35 : 35 }]}>
                                <Text style={{ color: '#000000' }}>{convertForShowData(endDate)}</Text>
                            </TouchableOpacity>

                            <DateTimePickerModal
                                isVisible={isEndDate}
                                mode='date'
                                onConfirm={handleConfirmEnd}
                                onCancel={hideDatePickerEnd}
                                date={new Date()}
                            />
                        </View>
                        <TouchableOpacity style={{ width: '100%', justifyContent: 'center', alignItems: 'center' }} onPress={() => getFilteredData()}>
                            <View style={{ width: '30%', alignItems: 'center', borderWidth: 1, borderColor: '#D5D5D5', paddingVertical: 8, borderRadius: 5 }}>
                                <Text style={{ color: '#000000' }}>{convertForShowData(textValue.Submit)}</Text>

                            </View>

                        </TouchableOpacity>
                    </View>
                    {dashboardDetails ? <View style={{ paddingTop: 40, width: '100%', alignItems: 'center' }}>
                        <ScrollView showsVerticalScrollIndicator={false}>
                            <View style={{ marginBottom: 120 }}>
                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url1, '1')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(textValue.Total_Linked_Mason)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.total_linked_mason)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url2, '2')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(textValue.Active_Mason)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.active_mason)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url3, '3')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(textValue.Verified_Lifting)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.verified_lifting)}</Text>

                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url4, '4')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(textValue.Unverified_Lifting)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.unverified_lifting)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url5, '5')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(textValue.Total_Ppc_Lifting_Bag)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.total_ppc_lifting_bags)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url6, '6')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(textValue.Total_Arc_Lifting_Bag)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.total_arc_lifting_bags)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url7, '7')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}> {convertForShowData(textValue.Mason_Net_Point)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.mason_net_points)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url8, '8')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}> {convertForShowData(textValue.Gift_Redeemed)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.gift_redeemed)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url9, '9')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}> {convertForShowData(textValue.Gift_pending)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.gift_pending)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url10, '10')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>  {convertForShowData(textValue.Gift_Delivered)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.gift_delivered)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url11, '11')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}> {convertForShowData(textValue.Query_Raised)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.query_raised)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url12, '12')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}> {convertForShowData(textValue.Query_pending)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.query_pending)}</Text>
                                    </View>
                                </TouchableOpacity>

                                <TouchableOpacity onPress={() => getDashBoardDetailsOther(url13, '13')} style={{ width: 240, borderWidth: 1, borderColor: 'red', padding: 20, marginTop: 10, borderRadius: 15 }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}> {convertForShowData(textValue.Query_resolved)} : </Text>
                                        <Text style={{ fontWeight: '800', color: '#000000' }}>{convertForShowData(dashboardDetails?.query_resolved)}</Text>
                                    </View>
                                </TouchableOpacity>

                            </View>
                        </ScrollView>
                    </View> : null}
                </View>
            </View>
            {loading ? <Loader /> : null}

            <Modal
                animationType='fade'
                transparent={true}
                visible={modalVisible}
                onRequestClose={(res) => {
                    setModalVisible(!modalVisible)
                }}
            >
                <View style={styles._modal_view}>
                    <View style={[styles._modal_view._view, { height: open === true ? 360 : 360 }]}>
                        <Text style={styles._modal_view._view._header}>{convertForShowData(textValue.UPDATE_LIFTING_DETAILS)}</Text>
                        <View style={styles._modal_view._input_section}>
                            <View style={styles._modal_view._input_section._dropdown_start_view}>
                                <Text style={styles._modal_view._input_section._dropdown_start_view._txt}>*</Text>
                            </View>
                            <View style={[styles._modal_view._input_section._dropdown_start_view._dropdown_view, { borderBottomColor: open === true ? '#00000000' : '#a8a8a8', borderLeftColor: open === true ? '#00000000' : '#a8a8a8', borderRightColor: open === true ? '#00000000' : '#a8a8a8', borderTopColor: open === true ? '#00000000' : '#a8a8a8', height: open === true ? Platform.OS == 'android' ? 248 : 'auto' : 55, borderBottomStartRadius: open === true ? 5 : 25, borderBottomEndRadius: open === true ? 5 : 25 }]}>
                                <DropDownPicker
                                    listMode='SCROLLVIEW'
                                    scrollViewProps={{
                                        nestedScrollEnabled: true,
                                    }}
                                    style={{
                                        backgroundColor: '#fff00000',
                                        borderColor: open ? '#a8a8a8' : '#00000000',
                                        borderRadius: 25,
                                        width: '90%',
                                        marginTop: 0

                                    }}
                                    open={open}
                                    value={value}
                                    items={products}
                                    setOpen={setOpen}
                                    setValue={setValue}
                                    placeholder={textValue.Select_Product}
                                    searchable={true}
                                    onChangeValue={(value) => {
                                        if (value) {
                                            setError(false)
                                            setErrorTxt('')
                                        }
                                    }}
                                    textStyle={{
                                        fontSize: 14,
                                    }}
                                    dropDownContainerStyle={{
                                        borderWidth: 1,
                                        borderColor: '#a8a8a8',
                                        borderRadius: 25,
                                        zIndex: 99999999,
                                        elevation: 1000,
                                        width: '90%',
                                        backgroundColor: '#fff'
                                    }}
                                    searchPlaceholder={textValue.Search + '...'}
                                    searchPlaceholderTextColor='#999'
                                    searchContainerStyle={{
                                        borderBottomColor: '#ddd',
                                    }}
                                    searchTextInputStyle={{
                                        color: '#000',
                                        borderColor: '#a8a8a8',
                                    }}
                                />
                            </View>

                            <View style={styles._modal_view._input_section._dropdown_start_view._input_view}>
                                <Text style={styles._modal_view._input_section._dropdown_start_view._input_view._start_mark}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Enter_Qty_No_Of_Bags}
                                    onChangeText={text => {
                                        setQty(text)
                                        setError(false)
                                        setErrorTxt('')
                                    }}
                                    value={convertForShowData(qty)}
                                    keyboardType='number-pad'
                                />
                            </View>
                            {error ? <View style={styles._modal_view._error_txt}>
                                <Text style={styles._modal_view._error_txt._txt}>{convertForShowData(errorTxt)}</Text>
                            </View> : null}

                            <View style={styles._modal_view._input_section._dropdown_start_view._input_view}>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Remarks}
                                    onChangeText={text => {
                                        setRemark(text)
                                    }}
                                    value={convertForShowData(remark)}
                                />
                            </View>

                        </View>

                        <View style={styles._modal_view._btn_section}>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={styles._modal_view._btn_section._touchableOpacity}
                                onPress={() => setModalVisible(false)}
                            >
                                <Text style={styles._modal_view._btn_section._touchableOpacity._txt}>{convertForShowData(textValue.CANCEL)}</Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={styles._modal_view._btn_section._touchableOpacity}
                                onPress={() => {
                                    form_validate()
                                }}
                            >
                                <Text style={styles._modal_view._btn_section._touchableOpacity._txt}>{convertForShowData(textValue.UPDATE)}</Text>
                            </TouchableOpacity>
                        </View>

                    </View>
                </View>
                {loading ? <Loader /> : null}
            </Modal>
        </SafeView>
    )
}

export default DashboardTe