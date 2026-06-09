import React, { useState, useEffect } from 'react'
import { SafeAreaView, Text, TextInput, View, Image, TouchableOpacity, Modal, Platform, ActivityIndicator, FlatList, } from 'react-native'
import DropDownPicker from 'react-native-dropdown-picker'
import styles from './LiftingHistoryStyle'
import { getApi, postApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import { searchItem } from '../../../helper/search/SearchItem'
import Icon from 'react-native-vector-icons/FontAwesome'
import moment from 'moment'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import DataStore from '../../../helper/constants/DataStore'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

var user_details
var _editable_obj

const LiftingHistory = props => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)

    const [openMnt, setOpenMnt] = useState(false)
    const [valueMnt, setValueMnt] = useState('')
    const [months, setMonths] = useState([])

    const [openYr, setOpenYr] = useState(false)
    const [valueYr, setValueYr] = useState('')
    const [yrs, setYrs] = useState([
        { label: convertForShowData('2022'), value: '2022' },
        { label: convertForShowData('2023'), value: '2023' },
        { label: convertForShowData('2024'), value: '2024' },
        { label: convertForShowData('2025'), value: '2025' },
        { label: convertForShowData('2026'), value: '2026' },
        { label: convertForShowData('2027'), value: '2027' },
        { label: convertForShowData('2028'), value: '2028' },
        { label: convertForShowData('2029'), value: '2029' },
        { label: convertForShowData('2030'), value: '2030' },
    ])

    const [liftingHistory, setLiftingHistory] = useState([])
    const [copyLiftingHistory, setCopyLiftingHistory] = useState([])

    const [searchTxt, setSearchTxt] = useState('')
    const [modalVisible, setModalVisible] = useState(false)

    const [flatlistLoader, setFlatListLoader] = useState(true)

    //For edit
    const [open, setOpen] = useState(false)
    const [value, setValue] = useState('')
    const [products, setProducts] = useState('')
    const [qty, setQty] = useState('')
    const [remark, setRemark] = useState('')
    const [error, setError] = useState(false)
    const [errorTxt, setErrorTxt] = useState('')

    useEffect(() => {

        switch (DataStore.language) {
            case 'Hindi':
                setMonths([
                    { label: 'जनवरी', value: 1 },
                    { label: 'फ़रवरी', value: 2 },
                    { label: 'मार्च', value: 3 },
                    { label: 'अप्रैल', value: 4 },
                    { label: 'मई', value: 5 },
                    { label: 'जून', value: 6 },
                    { label: 'जुलाई', value: 7 },
                    { label: 'अगस्त', value: 8 },
                    { label: 'सितंबर', value: 9 },
                    { label: 'अक्टूबर', value: 10 },
                    { label: 'नवंबर', value: 11 },
                    { label: 'दिसंबर', value: 12 },
                ])
                break
            case 'Assamese':
                setMonths([
                    { label: 'জানুৱাৰী', value: 1 },
                    { label: 'ফেব্ৰুৱাৰী', value: 2 },
                    { label: 'মাৰ্চ', value: 3 },
                    { label: 'এপ্ৰিল', value: 4 },
                    { label: 'মে', value: 5 },
                    { label: 'জুন', value: 6 },
                    { label: 'জুলাই', value: 7 },
                    { label: 'আগষ্ট', value: 8 },
                    { label: 'চেপ্তেম্বৰ', value: 9 },
                    { label: 'অক্টোবৰ', value: 10 },
                    { label: 'নবেম্বৰ', value: 11 },
                    { label: 'ডিচেম্বৰ', value: 12 },
                ])
                break
            case 'Bengali':
                setMonths([
                    { label: 'জানুয়ারি', value: 1 },
                    { label: 'ফেব্রুয়ারী', value: 2 },
                    { label: 'মার্চ', value: 3 },
                    { label: 'এপ্রিল', value: 4 },
                    { label: 'মে', value: 5 },
                    { label: 'জুন', value: 6 },
                    { label: 'জুলাই', value: 7 },
                    { label: 'আগষ্ট', value: 8 },
                    { label: 'সেপ্টেম্বর', value: 9 },
                    { label: 'অক্টোবর', value: 10 },
                    { label: 'নভেম্বর', value: 11 },
                    { label: 'ডিসেম্বর', value: 12 },
                ])
                break
            default:
                setMonths([
                    { label: 'January', value: 1 },
                    { label: 'February', value: 2 },
                    { label: 'March', value: 3 },
                    { label: 'April', value: 4 },
                    { label: 'May', value: 5 },
                    { label: 'June', value: 6 },
                    { label: 'July', value: 7 },
                    { label: 'August', value: 8 },
                    { label: 'September', value: 9 },
                    { label: 'October', value: 10 },
                    { label: 'November', value: 11 },
                    { label: 'December', value: 12 },
                ])
                break
        }
        getData()
    }, [])

    const getData = async () => {
        try {
            const value = await AsyncStorage.getItem('user_info')

            if (value !== null) {
                user_details = JSON.parse(value)
                get_lifting_history(1, [])
            }
        } catch (e) { }
    }

    const get_lifting_history = async (page_value, value) => {
        var formData = new FormData()
        formData.append('id', user_details?.data?.id)
        formData.append('role', user_details?.data?.role)
        formData.append('preferred_app_lang', selectedLanguage())
        
        postApiWithHeader(constants.get_lifting_history + `?page=${page_value}&preferred_app_lang=` + selectedLanguage(), formData)
            .then(response => {
                if (response.data.status) {
                    let data = response.data.data
                    var a = value
                    a = [...a, ...response.data.data]
                    setLiftingHistory(a)
                    setCopyLiftingHistory(a)
                    get_lifting_history(page_value + 1, a)
                    if (page_value == 1) {
                        get_products(1, [])
                    }
                } else {
                    setLoading(false)

                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else {
                        if (page_value == 1) {
                            showToast('error', response.data.msg)
                        }
                        setFlatListLoader(false)
                    }
                }
            })
            .catch(err => {
                
                setLoading(false)
                // showToast('error', messageList.t4)
            })
    }

    const showToast = (type, msg) => {
        Toast.show({
            type: type,
            text2: msg,
            text2NumberOfLines: 2
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
    const changeLanguage = () => {
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
                setLoading(false)
                                Toast.show({
                                    type: 'error',
                                    text2: 'Sorry google translations not working',
                                    text2NumberOfLines: 2
                                })
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
                    get_lifting_history(1, [])
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

    const get_products = async (page_value, value) => {
        getApi(constants.get_all_products + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    let data = response.data.data
                    let products = []
                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            label: data[i].name,
                            value: data[i].id,
                        }
                        products.push(obj)
                    }
                    var a = value
                    a = [...a, ...products]
                    setProducts(a)
                    get_products(page_value + 1, a)
                } else {
                    if (page_value == 1) { showToast('error', response.data.msg) }
                }
            })
            .catch(err => {
                
                setLoading(false)
                // showToast('error', messageList.t4)
            })
    }

    const filder_by_dropdown = async () => {
        let array_list = []
        setSearchTxt('')
        if (valueMnt && valueYr) {
            for (var i = 0; i < copyLiftingHistory.length; i++) {
                let month = moment(copyLiftingHistory[i].lifting_date, 'DD-MM-YYYY').format('M')
                let year = moment(copyLiftingHistory[i].lifting_date, 'DD-MM-YYYY').format('YYYY')
                if (month == valueMnt && year == valueYr) {
                    array_list.push(copyLiftingHistory[i])
                }
            }
            setLiftingHistory(array_list)
        } else if (valueYr) {
            for (var i = 0; i < copyLiftingHistory.length; i++) {
                let year = moment(copyLiftingHistory[i].lifting_date, 'DD-MM-YYYY').format('YYYY')
                if (year == valueYr) {
                    array_list.push(copyLiftingHistory[i])
                }
            }
            setLiftingHistory(array_list)
        } else {
            for (var i = 0; i < copyLiftingHistory.length; i++) {
                let month = moment(copyLiftingHistory[i].lifting_date, 'DD-MM-YYYY').format('M')
                if (month == valueMnt) {
                    array_list.push(copyLiftingHistory[i])
                }
            }
            setLiftingHistory(array_list)
        }
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

    const renderItem = ({ item }) => (
        <View
            style={{ width: '100%', justifyContent: 'center', alignItems: 'center' }}>
            {user_details?.data?.role != 2 ? (
                <View style={{ width: '90%', margin: 10, position: 'relative' }}>
                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.MASON)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.mason_name)}
                            </Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.PRODUCTS)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.product_name)}
                            </Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.NO_OF_BAGS)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.qty)}</Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.LIFTING_DATE)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.lifting_date)}
                            </Text>
                        </View>
                    </View>

                    <View
                        style={{
                            flexDirection: 'row',
                            flex: 3,
                            marginTop: 5,
                            marginBottom: 8,
                        }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {item?.is_verified === 1 ? convertForShowData(item?.point) : convertForShowData(textValue.In_progress)}
                            </Text>
                        </View>
                    </View>

                    <View
                        style={{
                            flexDirection: 'row',
                            flex: 3,
                            marginTop: 5,
                            marginBottom: 8,
                        }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DEALER)}/{convertForShowData(textValue.RSSD)}/</Text>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.SUB_DEALER)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.dealer_name)}
                            </Text>
                        </View>
                    </View>

                    {item?.request_send_to === 2 ? <View
                        style={{
                            flexDirection: 'row',
                            flex: 3,
                            marginTop: 5,
                            marginBottom: 8,
                        }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.STAR_SAATHI_STATUS)}</Text>

                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {item?.star_sathi_status === 0 ? convertForShowData(textValue.Pending) : (item?.star_sathi_status === 1 ? convertForShowData(textValue.Approved) : convertForShowData(textValue.Rejected))}
                            </Text>
                        </View>
                    </View> : null}

                    {item?.request_send_to === 2 && item?.action_taken_at !== null ? <View
                        style={{
                            flexDirection: 'row',
                            flex: 3,
                            marginTop: 5,
                            marginBottom: 8,
                        }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.APPROVED_REJECTED_DATE_AND_TIME)}</Text>

                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item.action_taken_at)}
                            </Text>
                        </View>
                    </View> : null}
                    <View
                        style={{
                            width: '100%',
                            height: 1,
                            backgroundColor: 'gray',
                            position: 'absolute',
                            bottom: 0,
                        }}></View>
                </View>
            ) : (
                <View style={{ width: '90%', margin: 10, position: 'relative' }}>
                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.PRODUCTS)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.product_name)}
                            </Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.NO_OF_BAGS)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.qty)}</Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.LIFTING_DATE)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.lifting_date)}
                            </Text>
                        </View>
                    </View>

                    <View
                        style={{
                            flexDirection: 'row',
                            flex: 3,
                            marginTop: 5,
                            marginBottom: 8,
                        }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {item?.is_verified === 1 ? convertForShowData(item?.point) : convertForShowData(textValue.In_progress)}
                            </Text>
                        </View>
                    </View>

                    <View
                        style={{
                            flexDirection: 'row',
                            flex: 3,
                            marginTop: 5,
                            marginBottom: 8,
                        }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DEALER)}/{convertForShowData(textValue.RSSD)}/</Text>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.SUB_DEALER)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.dealer_name)}
                            </Text>
                        </View>
                    </View>

                    {item?.request_send_to === 2 ? <View
                        style={{
                            flexDirection: 'row',
                            flex: 3,
                            marginTop: 5,
                            marginBottom: 8,
                        }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.STAR_SAATHI_STATUS)}</Text>

                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {item?.star_sathi_status === 0 ? convertForShowData(textValue.Pending) : item?.star_sathi_status === 1 ? convertForShowData(textValue.Approved) : convertForShowData(textValue.Rejected)}
                            </Text>
                        </View>
                    </View> : null}

                    {item?.request_send_to === 2 && item?.action_taken_at !== null ? <View
                        style={{
                            flexDirection: 'row',
                            flex: 3,
                            marginTop: 5,
                            marginBottom: 8,
                        }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.APPROVED_REJECTED_DATE_AND_TIME)}</Text>

                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item.action_taken_at)}
                            </Text>
                        </View>
                    </View> : null}

                    <View
                        style={{
                            width: '100%',
                            height: 1,
                            backgroundColor: 'gray',
                            position: 'absolute',
                            bottom: 0,
                        }}></View>
                </View>
            )}
        </View>
    )

    const renderFooter = () => {
        if (!flatlistLoader) return null
        return (
            <View style={{
                paddingHorizontal: 20,
                paddingTop: 20,
                alignItems: 'center',
            }}>
                <ActivityIndicator size='large' color='#ee1d23' />
            </View>
        )
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._bgColor}>
                <View style={styles._upperView}>
                    <TouchableOpacity
                        style={styles._upperView._back_btn}
                        onPress={() =>setTimeout(()=>{
                                props.navigation.goBack()
                            },500)}>
                        <Image
                            style={styles._upperView._back_btn._img}
                            source={Icons.back}
                        />
                    </TouchableOpacity>

                    <View style={{ justifyContent: 'center', alignItems: 'center' }}>
                        <Text
                            style={[
                                styles._upperView._txt,
                                { marginBottom: user_details?.data?.role == 2 ? 0 : 20 },
                            ]}>
                            {convertForShowData(textValue.LIFTING_HISTORY)}
                        </Text>
                        {user_details?.data?.role != 2 ? (
                            <View style={styles._upperView._search_input}>
                                <TextInput
                                    placeholderTextColor='#fff'
                                    style={styles._upperView._search_input._txt_input}
                                    placeholder={textValue.Search_Name + '/' + textValue.Mobile_No + '/' + textValue.Aadhar}
                                    value={convertForShowData(searchTxt)}
                                    onChangeText={text => {
                                        setSearchTxt(text)

                                        let filterData = searchItem(copyLiftingHistory, text)
                                        setLiftingHistory(filterData)
                                    }} />
                                <TouchableOpacity
                                    style={styles._upperView._search_input._search_icon_view}>
                                    <Image
                                        style={
                                            styles._upperView._search_input._search_icon_view._img
                                        }
                                        source={Icons.search}
                                    />
                                </TouchableOpacity>
                            </View>
                        ) : null}
                    </View>
                </View>

                <View style={styles._lowerView}>
                    <View
                        style={{
                            justifyContent: 'center',
                            alignItems: 'center',
                            marginTop: 10,
                            marginBottom: -8,
                            zIndex: 999,
                        }}>
                        <View
                            style={[
                                styles._dropdown_view,
                                {
                                    height:
                                        Platform.OS == 'android'
                                            ? openMnt || openYr
                                                ? 178
                                                : 35
                                            : 35,
                                },
                            ]}>
                            <View
                                style={[
                                    styles._dropdown_view._left_view,
                                    {
                                        height:
                                            Platform.OS == 'android' ? (openMnt ? 178 : 35) : 35,
                                    },
                                ]}>
                                <DropDownPicker
                                    listMode='SCROLLVIEW'
                                    scrollViewProps={{
                                        nestedScrollEnabled: true,
                                    }}
                                    style={{
                                        backgroundColor: '#fff00000',
                                        borderColor: '#a8a8a800',
                                        borderRadius: 25,
                                        width: '90%',
                                        marginTop: -10,
                                    }}
                                    open={openMnt}
                                    value={valueMnt}
                                    items={months}
                                    setOpen={setOpenMnt}
                                    setValue={setValueMnt}
                                    placeholder={textValue.Month}
                                    onChangeValue={value => {
                                        if (value) {
                                            filder_by_dropdown()
                                        }
                                    }}
                                    textStyle={{
                                        fontSize: 12,
                                    }}
                                    dropDownContainerStyle={{
                                        borderWidth: 1,
                                        borderColor: '#a8a8a8',
                                        elevation: 1000,
                                        width: '100%',
                                        backgroundColor: '#fff',
                                        marginTop: -20,
                                        height: 150,
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
                            <View
                                style={[
                                    styles._dropdown_view._right_view,
                                    { height: Platform.OS == 'android' ? (openYr ? 178 : 35) : 35 },
                                ]}>
                                <DropDownPicker
                                    listMode='SCROLLVIEW'
                                    scrollViewProps={{
                                        nestedScrollEnabled: true,
                                    }}
                                    style={{
                                        backgroundColor: '#fff00000',
                                        borderColor: '#a8a8a800',
                                        borderRadius: 25,
                                        width: '90%',
                                        marginTop: -10,
                                        marginLeft: 10,
                                    }}
                                    open={openYr}
                                    value={valueYr}
                                    items={yrs}
                                    setOpen={setOpenYr}
                                    setValue={setValueYr}
                                    placeholder={textValue.Year}
                                    onChangeValue={value => {
                                        if (value) {
                                            filder_by_dropdown()
                                        }
                                    }}
                                    textStyle={{
                                        fontSize: 12,
                                    }}
                                    dropDownContainerStyle={{
                                        borderWidth: 1,
                                        borderColor: '#a8a8a8',
                                        elevation: 1000,
                                        width: '100%',
                                        backgroundColor: '#fff',
                                        marginTop: -20,
                                        height: 150,
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

                            <TouchableOpacity
                                style={{ height: '100%', width: 40, marginLeft: 5, marginTop: 5 }}
                                activeOpacity={0.8}
                                onPress={() => {
                                    setValueMnt('')
                                    setValueYr('')
                                    getData()
                                }}>
                                <Icon name='refresh' size={20} color='#900' />
                            </TouchableOpacity>
                        </View>
                    </View>

                    <FlatList
                        data={liftingHistory}
                        renderItem={renderItem}
                        ListFooterComponent={renderFooter}
                        keyExtractor={item => item.lifting_id}
                    />
                </View>
            </View>
            {/* {loading ? <Loader /> : null} */}

            <Modal
                animationType='fade'
                transparent={true}
                visible={modalVisible}
                onRequestClose={res => {
                    setModalVisible(!modalVisible)
                }}>
                <View style={styles._modal_view}>
                    <View
                        style={[
                            styles._modal_view._view,
                            { height: open === true ? 360 : 360 },
                        ]}>
                        <Text style={styles._modal_view._view._header}>
                            {convertForShowData(textValue.UPDATE_LIFTING_DETAILS)}
                        </Text>

                        <View style={styles._modal_view._input_section}>
                            <View
                                style={styles._modal_view._input_section._dropdown_start_view}>
                                <Text
                                    style={
                                        styles._modal_view._input_section._dropdown_start_view._txt
                                    }>
                                    *
                                </Text>
                            </View>

                            <View
                                style={[
                                    styles._modal_view._input_section._dropdown_start_view
                                        ._dropdown_view,
                                    {
                                        borderBottomColor: open === true ? '#00000000' : '#a8a8a8',
                                        borderLeftColor: open === true ? '#00000000' : '#a8a8a8',
                                        borderRightColor: open === true ? '#00000000' : '#a8a8a8',
                                        borderTopColor: open === true ? '#00000000' : '#a8a8a8',
                                        height:
                                            open === true
                                                ? Platform.OS == 'android'
                                                    ? 248
                                                    : 'auto'
                                                : 55,
                                        borderBottomStartRadius: open === true ? 5 : 25,
                                        borderBottomEndRadius: open === true ? 5 : 25,
                                    },
                                ]}>
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
                                        marginTop: 0,
                                    }}
                                    open={open}
                                    value={value}
                                    items={products}
                                    setOpen={setOpen}
                                    setValue={setValue}
                                    placeholder={textValue.Select_Product}
                                    searchable={true}
                                    onChangeValue={value => {
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
                                        backgroundColor: '#fff',
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

                            <View
                                style={
                                    styles._modal_view._input_section._dropdown_start_view
                                        ._input_view
                                }>
                                <Text
                                    style={
                                        styles._modal_view._input_section._dropdown_start_view
                                            ._input_view._start_mark
                                    }>
                                    *
                                </Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Enter_Qty_No_Of_Bags}
                                    onChangeText={text => {
                                        setQty(text)
                                        setError(false)
                                        setErrorTxt('')
                                    }}
                                    value={convertForShowData(qty)}
                                    keyboardType='number-pad' />
                            </View>
                            {error ? (
                                <View style={styles._modal_view._error_txt}>
                                    <Text style={styles._modal_view._error_txt._txt}>
                                        {convertForShowData(errorTxt)}
                                    </Text>
                                </View>
                            ) : null}

                            <View
                                style={
                                    styles._modal_view._input_section._dropdown_start_view
                                        ._input_view
                                }>
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
                                onPress={() => setModalVisible(false)}>
                                <Text
                                    style={
                                        styles._modal_view._btn_section._touchableOpacity._txt
                                    }>
                                    {convertForShowData(textValue.CANCEL)}
                                </Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={styles._modal_view._btn_section._touchableOpacity}
                                onPress={() => {
                                    form_validate()
                                }}>
                                <Text
                                    style={
                                        styles._modal_view._btn_section._touchableOpacity._txt
                                    }>
                                    {convertForShowData(textValue.UPDATE)}
                                </Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
                {loading ? <Loader /> : null}
            </Modal>
        </SafeView>
    )
}

export default LiftingHistory
