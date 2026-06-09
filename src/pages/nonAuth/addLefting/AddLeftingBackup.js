import React, { useRef, useState, useEffect, useCallback } from 'react'
import { SafeAreaView, ScrollView, Text, TextInput, View, Image, TouchableOpacity, Platform, Modal, TouchableWithoutFeedback } from 'react-native'
import styles from './AddLeftingStyle'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import DropDownPicker from 'react-native-dropdown-picker'
import moment from 'moment'
import Toast from 'react-native-toast-message'
import { getApi, postApi, postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import OtpInputs from 'react-native-otp-inputs'
import Icon from 'react-native-vector-icons/AntDesign'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import DataStore from '../../../helper/constants/DataStore'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

var user_details
const AddLefting = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)

    const [isDatePickerVisible, setDatePickerVisibility] = useState(false)
    const [selectedDate, setSelectedDate] = useState(moment(new Date()).format('Do , MMMM , YYYY'))

    const [open, setOpen] = useState(false)
    const [value, setValue] = useState('')

    const [openMason, setOpenMason] = useState(false)
    const [valueMason, setValueMason] = useState('')

    const [openDealerRssd, setOpenDealerRssd] = useState(false)
    const [valueDealerRssd, setValueDealerRssd] = useState('')

    const [qty, setQty] = useState('')
    const [remarks, setRemarks] = useState('')

    const [products, setProducts] = useState([])
    const [masonList, setMasonList] = useState([])

    const [dealerRssdList, setDealerRssdList] = useState([])
    const [dealerRssdListCopy, setDealerRssdListCopy] = useState([])
    const [dealerRssdPhone, setDealerRssdPhone] = useState('')

    const [modalVisible, setModalVisible] = useState(false)

    const [liftingMinimumDate, setLiftingMinimumDate] = useState(2)

    const otpRef = useRef()
    const [otp, setOtp] = useState('')

    const [infoView, setInfoView] = useState(false)
    const [infoTxt, setInfoTxt] = useState('')

    const [inActive, setInActive] = useState(false)
    const [starSathibtn, setStarsathibtn] = useState(false)

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            getSettings()
            getData()
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    const getSettings = async () => {
        getApiWithHeader(constants.app_registration_link_visible + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {

                if (response.data.status) {
                    if (response?.data?.data[0]?.lifting_send_star_sathi_app_button == '1') {
                        setStarsathibtn(true)
                    }
                }
            })
            .catch(err => {

            })
    }

    const getData = async () => {
        try {
            const value = await AsyncStorage.getItem('user_info')

            if (value !== null) {
                user_details = JSON.parse(value)

                setValueMason(user_details?.data?.id)

                setLoading(true)
                get_products(1, [])
            }
        } catch (e) {
            // error reading value
        }
    }

    const get_products = async (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        getApi(constants.get_all_products + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
                    let data = response.data.data
                    let products = []

                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            label: data[i].name,
                            value: data[i].id
                        }

                        products.push(obj)
                    }
                    var a = value
                    a = [...a, ...products]
                    setProducts(a)
                    // get_mason_list()
                    if (page_value == 1) { get_all_dealers(1, [], []) }
                    get_products(page_value + 1, a)
                }
                else {
                    setLoading(false)
                    showToast('error', response.data.msg)
                }
            })
            .catch(err => {

                setLoading(false)
                // showToast('error', messageList.t4)
            })
    }

    const get_all_dealers = async (page_value, value, value1) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        getApiWithHeader(constants.get_all_dealers + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                setLoading(false)
                if (response.data.status) {
                    let data = response.data.data
                    let dealer_rssd = []

                    for (var i = 0; i < data.length; i++) {
                        let phone = data[i]?.phone ? ' ' + data[i]?.phone : ''
                        let aadhaar_no = data[i]?.aadhaar_no ? ' ' + data[i]?.phone : ''

                        let obj = {
                            // label: data[i]?.name + phone + aadhaar_no,
                            label: data[i]?.name,
                            value: data[i].id
                        }

                        dealer_rssd.push(obj)
                    }

                    var a = value
                    a = [...a, ...response.data.data]
                    var b = value1
                    b = [...b, ...dealer_rssd]

                    setDealerRssdList(b)
                    setDealerRssdListCopy(a)

                    if (page_value == 1) { lifting_date_disable_days_count() }
                    get_all_dealers(page_value + 1, a, b)
                }
                else {
                    // showToast('error', response.data.msg)
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    }
                    else {
                        if (page_value == 1) {
                            showToast('error', response?.data?.msg)
                        }
                    }
                }
            })
            .catch(err => {

                setLoading(false)
                // showToast('error', messageList.t4)
            })
    }

    const lifting_date_disable_days_count = async (id) => {

        getApiWithHeader(constants.app_registration_link_visible + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {

                if (response.data.status) {
                    setLiftingMinimumDate(response?.data?.data[0]?.app_lifting_date)
                }
            })
            .catch(err => {

            })
    }

    const get_mason_list = async (id) => {

        getApiWithHeader(constants.get_my_mason + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {

                if (response.data.status) {
                    let data = response.data.data
                    let masonList = []

                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            label: data[i].mason_name + '(' + data[i].mason_phone + '/' + data[i].mason_aadhaar_no + ')',
                            value: data[i].mason_id
                        }

                        masonList.push(obj)
                    }

                    setMasonList(masonList)

                    get_all_dealers()
                }
                else {
                    setLoading(false)
                    showToast('error', response.data.msg)
                }
            })
            .catch(err => {

                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const showDatePicker = () => {
        setDatePickerVisibility(true)
    }

    const hideDatePicker = () => {
        setDatePickerVisibility(false)
    }

    const handleConfirm = (date) => {
        setSelectedDate(moment(date).format('Do , MMMM , YYYY'))
        hideDatePicker()
    }

    const form_validation = () => {

        // if (valueMason == null) {
        //     showToast('error', 'Please select mason.')
        // }
        // else if (user_details?.data?.role == 1 && valueDealerRssd == null) {
        //     showToast('error', 'Please select dealer/rssd.')
        // }

        if (valueDealerRssd == null) {
            showToast('error', messageList.t30)
        }
        else if (value == null) {
            showToast('error', messageList.t31)
        }
        else if (qty == '') {
            showToast('error', messageList.t32)
        }
        else if (qty.includes('.')) {
            showToast('error', messageList.t33)
        }
        else if (selectedDate == '') {
            showToast('error', messageList.t34)
        }
        else {
            if (qty >= 1 && qty <= 200) {
                setInActive(false)
                send_otp()
            }
            else {
                var message = DataStore.language == 'English' ?
                    `No of bags range should be between ${min} to ${max}` : (
                        DataStore.language == 'Hindi' ?
                            `बैगों की संख्या ${min} से ${max} के बीच होनी चाहिए` : (
                                DataStore.language == 'Assamese' ?
                                    `বেগৰ কোনো এটা পৰিসৰ ${min}ৰ পৰা ${max}ৰ ভিতৰত হ'ব লাগে।` : (
                                        DataStore.language == 'Bengali' ?
                                            `ব্যাগের পরিসীমা ${min} থেকে ${max} এর মধ্যে হওয়া উচিত` : `No of bags range should be between ${min} to ${max}`
                                    )
                            )
                    )
                showToast('error', message)
            }
        }
        // setModalVisible(true)
    }
    const changeLanguage = (val) => {
        setLoading(true)
        var arr = [remarks]
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
                add_lifting(val, result.data.translations[0].translatedText)
            })
            .catch(err => {
                add_lifting(val, arr[0])
                // setLoading(false)
                // Toast.show({
                //     type: 'error',
                //     text2: 'Sorry google translations not working',
                //     text2NumberOfLines: 2
                // })
            })
    }
    const add_lifting = async (val, remark) => {

        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('mason_ids', '[' + valueMason + ']')
        formData.append('product_id', value)
        formData.append('qty', convertForUploadData(qty))
        formData.append('lifting_date', convertForUploadData(moment(selectedDate, 'Do , MMMM , YYYY').format('DD-MM-yyyy')))
        formData.append('remark', convertForUploadData(remark))
        formData.append('img', '')
        formData.append('dealer_rssd_id', valueDealerRssd)
        formData.append('req_type', convertForUploadData(val))
        formData.append('preferred_app_lang', selectedLanguage())

        postApiWithHeader(constants.add_lifting, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {

                    showToast('success', response.data.msg)
                    props.navigation.goBack()
                }
                else {
                    showToast('error', response.data.msg)
                    props.navigation.goBack()
                }
            })
            .catch(err => {

                setLoading(false)
                // show_toast('error', 'Unknown error has occurred')
                showToast('error', messageList.t4)
            })
    }

    const showToast = (type, msg) => {
        Toast.show({
            type: type,
            //   text1: 'Hello',
            text2: msg,
            text2NumberOfLines: 2
        })
    }

    const handleChange = code => {
        setOtp(code)
    }

    const send_otp = async () => {

        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('phone', convertForUploadData(dealerRssdPhone))
        formData.append('otp_purpose', 'add_lifting')
        formData.append('preferred_app_lang', selectedLanguage())

        postApi(constants.send_otp_to_new_number, formData)
            .then(response => {
                setLoading(false)

                if (response.data.status) {
                    setModalVisible(true)

                    setInfoTxt(response.data.msg)
                    infoType = 'success'
                    setInfoView(true)
                    setTimeout(() => {
                        setInfoView(false)
                    }, 2000)
                }
                else {
                    // showToast('error', messageList.error, response.data.msg)
                    setInfoTxt(response.data.msg)
                    infoType = 'error'
                    setInfoView(true)
                    setTimeout(() => {
                        setInfoView(false)
                    }, 2000)
                }
            })
            .catch(err => {

                setLoading(false)
                // showToast('error', messageList.t4)
                setInfoTxt(messageList.t4)
                infoType = 'error'
                setInfoView(true)
                setTimeout(() => {
                    setInfoView(false)
                }, 2000)
            })
    }

    const verify_otp = async (val) => {
        if (inActive == true) {

        } else {
            setLoading(true)

            var FormData = require('form-data')
            var formData = new FormData()
            formData.append('phone', convertForUploadData(dealerRssdPhone))
            formData.append('otp', convertForUploadData(otp))
            formData.append('preferred_app_lang', selectedLanguage())
            // formData.append('req_type',val)
            setInActive(true)

            postApi(constants.verify_phone, formData)
                .then(response => {
                    setLoading(false)
                    if (response.data.status) {
                        setModalVisible(false)
                        setLoading(true)
                        changeLanguage(1)

                    }
                    else {
                        setInActive(false)
                        setInfoTxt(response.data.msg)
                        infoType = 'error'
                        setInfoView(true)
                        setTimeout(() => {
                            setInfoView(false)
                        }, 2000)
                    }
                })
                .catch(err => {

                    setInActive(false)
                    setLoading(false)
                    // showToast('error', messageList.t4)
                    setInfoTxt(messageList.t4)
                    infoType = 'error'
                    setInfoView(true)
                    setTimeout(() => {
                        setInfoView(false)
                    }, 2000)
                })

        }

    }

    const _logout = async () => {
        try {
            // await AsyncStorage.clear()

            const keys = ['user_info', 'access_token']
            await AsyncStorage.multiRemove(keys)
            // props.navigation.navigate('AuthStack')
            props.navigation.reset({
                index: 0,
                routes: [{ name: 'AuthStack' }],
            })
        } catch (e) {
            // remove error
        }
    }


    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._bgColor}>

                <View style={styles._upperView}>
                    <Text style={styles._upperView._txt}>{convertForShowData(textValue.ADD_LIFTING)}</Text>
                    <TouchableOpacity style={styles._upperView._back}
                        onPress={() =>setTimeout(()=>{
                                props.navigation.goBack()
                            },500)}
                    >
                        <Image style={styles._upperView._back._img} source={Icons.back} />
                    </TouchableOpacity>
                </View>

                <View style={styles._lowerView}>

                    <ScrollView style={styles._lowerView._scrollView}>
                        <View style={styles._lowerView._scrollView._view}>

                            {/* <View style={{ width: '90%', marginBottom: -33, height: 15, position: 'relative' }}>
                                <Text style={{ color: '#ee1d23', position: 'absolute', right: 0, top: -2 }}>*</Text>
                            </View> */}

                            {/* <View style={{ zIndex: 100, marginTop: 20, borderRightColor: openMason ? '#00000000' : '#a8a8a8', borderLeftColor: openMason ? '#00000000' : '#a8a8a8', borderTopColor: openMason ? '#00000000' : '#a8a8a8', borderBottomColor: openMason ? '#00000000' : '#a8a8a8', borderWidth: 1, height: openMason ? Platform.OS == 'android' ? 248 : 55 : 55, borderTopEndRadius: 25, borderTopStartRadius: 25, borderBottomStartRadius: openMason === true ? 5 : 25, borderBottomEndRadius: openMason === true ? 5 : 25 }}>

                                <DropDownPicker
                                    listMode='SCROLLVIEW'
                                    scrollViewProps={{
                                        nestedScrollEnabled: true,
                                    }}

                                    style={{
                                        backgroundColor: '#fff00000',
                                        // borderColor: '#a8a8a800',
                                        borderColor: openMason ? '#a8a8a8' : '#00000000',
                                        borderRadius: 25,
                                        width: '90%',

                                    }}
                                    open={openMason}
                                    value={valueMason}
                                    items={masonList}
                                    setOpen={setOpenMason}
                                    setValue={setValueMason}
                                    placeholder='Select Mason'
                                    searchable={true}

                                    onChangeValue={(value) => {

                                        if (value) {

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
                                    searchPlaceholder='Search...'
                                    searchPlaceholderTextColor='#999'
                                    searchContainerStyle={{
                                        borderBottomColor: '#ddd',
                                    }}
                                    searchTextInputStyle={{
                                        color: '#000',
                                        borderColor: '#a8a8a8',
                                    }}

                                />

                            </View> */}

                            {user_details?.data?.role == 3 || user_details?.data?.role == 4 ? null : <>
                                <View style={{ width: '90%', marginBottom: -33, height: 30, position: 'relative' }}>
                                    <Text style={{ color: '#ee1d23', position: 'absolute', right: 0, top: 12 }}>*</Text>
                                </View>

                                <View style={{ marginTop: openMason ? Platform.OS == 'android' ? -175 : 20 : 20, zIndex: 99, borderRightColor: openDealerRssd ? '#00000000' : '#a8a8a8', borderLeftColor: openDealerRssd ? '#00000000' : '#a8a8a8', borderTopColor: openDealerRssd ? '#00000000' : '#a8a8a8', borderBottomColor: openDealerRssd ? '#00000000' : '#a8a8a8', borderWidth: 1, height: openDealerRssd ? Platform.OS == 'android' ? 248 : 55 : 55, borderTopEndRadius: 25, borderTopStartRadius: 25, borderBottomStartRadius: openDealerRssd === true ? 5 : 25, borderBottomEndRadius: openDealerRssd === true ? 5 : 25 }}>

                                    <DropDownPicker
                                        listMode='SCROLLVIEW'
                                        scrollViewProps={{
                                            nestedScrollEnabled: true,
                                        }}

                                        style={{
                                            backgroundColor: '#fff00000',
                                            // borderColor: '#a8a8a800',
                                            borderColor: openDealerRssd ? '#a8a8a8' : '#00000000',
                                            borderRadius: 25,
                                            width: '90%',

                                        }}
                                        open={openDealerRssd}
                                        value={valueDealerRssd}
                                        items={dealerRssdList}
                                        setOpen={setOpenDealerRssd}
                                        setValue={setValueDealerRssd}
                                        placeholder={textValue.Select_Dealer_Rssd_Sub_Dealer}
                                        searchable={true}

                                        onChangeValue={(value) => {
                                            if (value) {
                                                for (var i = 0; i < dealerRssdListCopy.length; i++) {
                                                    if (value == dealerRssdListCopy[i].id) {
                                                        setDealerRssdPhone(dealerRssdListCopy[i].phone)
                                                    }
                                                }
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
                            </>}


                            <View style={{ width: '90%', marginBottom: -33, height: 30, position: 'relative' }}>
                                <Text style={{ color: '#ee1d23', position: 'absolute', right: 0, top: 12 }}>*</Text>
                            </View>

                            <View style={{ marginTop: user_details?.data?.role == 1 ? openDealerRssd ? Platform.OS == 'android' ? -175 : 20 : 20 : openMason ? Platform.OS == 'android' ? -175 : 20 : 20, zIndex: 98, borderRightColor: open ? '#00000000' : '#a8a8a8', borderLeftColor: open ? '#00000000' : '#a8a8a8', borderTopColor: open ? '#00000000' : '#a8a8a8', borderBottomColor: open ? '#00000000' : '#a8a8a8', borderWidth: 1, height: open ? Platform.OS == 'android' ? 248 : 55 : 'auto', borderTopEndRadius: 25, borderTopStartRadius: 25, borderBottomStartRadius: open === true ? 5 : 25, borderBottomEndRadius: open === true ? 5 : 25 }}>
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

                                    textStyle={{
                                        fontSize: 14,
                                    }}

                                    dropDownContainerStyle={{
                                        borderWidth: 1,
                                        borderColor: '#a8a8a8',
                                        zIndex: 99999999,
                                        borderRadius: 25,
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

                            <View style={[styles._lowerView._scrollView._input, { marginTop: open ? Platform.OS == 'android' ? -175 : 15 : 15 }]}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Enter_Qty_No_Of_Bags}
                                    onChangeText={text => setQty(text)}
                                    value={convertForShowData(qty)}
                                    keyboardType='number-pad'
                                />
                            </View>

                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <Text style={styles._lowerView._scrollView._input._txt_input}>{convertForShowData(selectedDate)}</Text>
                                <TouchableOpacity
                                    onPress={() => {
                                        showDatePicker()
                                    }}
                                    style={styles._lowerView._scrollView._dob}>
                                    <Image style={styles._lowerView._scrollView._dob._img} source={Icons.calender} />
                                </TouchableOpacity>
                            </View>

                            <View style={styles._lowerView._scrollView._input_area}>
                                {/* <Text style={{ position: 'absolute', top: -4, left: 0, color: '#ee1d23' }}>*</Text> */}
                                <TextInput
                                    multiline={true}
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Remarks}
                                    onChangeText={text => setRemarks(text)}
                                    value={convertForShowData(remarks)}
                                />
                            </View>

                            <TouchableOpacity
                                onPress={() => {
                                    form_validation()
                                }}
                                style={styles._lowerView._btn}>
                                <Text style={styles._lowerView._btn._txt}>{convertForShowData(textValue.Submit)}</Text>
                            </TouchableOpacity>
                        </View>
                    </ScrollView>
                </View>
            </View>

            <DateTimePickerModal
                isVisible={isDatePickerVisible}
                mode='date'
                onConfirm={handleConfirm}
                onCancel={hideDatePicker}
                maximumDate={new Date()}
                minimumDate={new Date(moment().subtract(liftingMinimumDate, 'days'))}
                date={new Date()}
            // maximumDate={new Date(moment().subtract(18, 'years'))}
            />
            {loading ? <Loader /> : null}

            <Modal
                animationType='fade'
                transparent={true}
                visible={modalVisible}
                onRequestClose={(res) => {
                    setModalVisible(!modalVisible)
                }}
            >
                <TouchableWithoutFeedback>
                    <View style={{
                        height: '100%',
                        width: '100%',
                        backgroundColor: '#00000095',
                        justifyContent: 'center',
                        alignItems: 'center',
                        position: 'relative'
                    }}>
                        <View style={{ width: '90%', backgroundColor: '#fff', borderRadius: 10, alignItems: 'center', padding: 10 }}>
                            <Text style={{ fontSize: 18, fontWeight: '700' }}>{convertForShowData(textValue.ENTER_OTP)}</Text>
                            <View style={{
                                width: '70%',
                                height: 50,
                                marginTop: '2%',
                                marginLeft: 10,
                            }}>
                                <OtpInputs
                                    clearTextOnFocus
                                    handleChange={handleChange}
                                    keyboardType='phone-pad'
                                    numberOfInputs={4}
                                    ref={otpRef}
                                    selectTextOnFocus={false}
                                    inputStyles={{
                                        borderColor: '#a8a8a8',
                                        borderWidth: 1,
                                        borderRadius: 5,
                                        textAlign: 'center',
                                        fontSize: 18,
                                        fontWeight: '600',
                                        width: 45,
                                        height: 45,
                                        backgroundColor: '#fff',
                                        color: '#000'
                                    }}
                                />
                            </View>
                            <View style={styles._btn_section}>
                                <Text style={styles._btn_section._txt}>{convertForShowData(textValue.Didnt_receive_an_OTP)}</Text>
                                <TouchableOpacity
                                    activeOpacity={0.8}
                                    onPress={() => {
                                        send_otp()
                                    }}
                                >
                                    <Text style={styles._btn_section._resend_btn}>{convertForShowData(textValue.Resend_OTP)}</Text>
                                </TouchableOpacity>
                            </View>

                            <TouchableOpacity
                                onPress={() => {
                                    verify_otp()
                                }}
                                style={styles._lowerView._btn}
                                activeOpacity={0.8}>
                                <Text style={styles._lowerView._btn._txt}>{convertForShowData(textValue.Verify)}</Text>
                            </TouchableOpacity>
                            {starSathibtn == true ? <View style={{ width: '100%', justifyContent: 'center', alignItems: 'center', paddingTop: 4 }}>
                                <Text>{convertForShowData(textValue.Or)}</Text>
                            </View> : null}
                            {starSathibtn == true ? <TouchableOpacity
                                onPress={() => {
                                    changeLanguage(2)

                                }}
                                style={styles._lowerView._btn}
                                activeOpacity={0.8}>
                                <Text style={styles._lowerView._btn._txt}>{convertForShowData(textValue.Verify_from_Star_Saathi)}</Text>
                            </TouchableOpacity> : null}
                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={{
                                    height: 35,
                                    width: 35,
                                    justifyContent: 'center',
                                    alignItems: 'center',
                                    position: 'absolute',
                                    right: 2,
                                    top: 2,
                                    backgroundColor: '#fff',
                                    borderRadius: 5,
                                }}
                                onPress={() => {
                                    setModalVisible(false)
                                }}
                            >
                                <Icon name='closecircle' size={25} color='#ee1d23' />
                            </TouchableOpacity>

                        </View>
                        {loading ? <Loader /> : null}
                        {infoView ? <View style={{ width: '100%', alignItems: 'center', position: 'absolute', top: '10%' }}>
                            <View style={{
                                width: '90%', height: 50, backgroundColor: '#fff', justifyContent: 'center', padding: 10, shadowColor: '#000000',
                                shadowOffset: {
                                    width: 0,
                                    height: 3
                                },
                                shadowRadius: 5,
                                shadowOpacity: 1.0,
                                elevation: 5,
                                borderRadius: 8,
                                overflow: 'hidden'
                            }}>
                                <Text numberOfLines={2} style={{ fontSize: 12, fontWeight: '600', marginLeft: 5 }}>{convertForShowData(infoTxt)}</Text>
                                <View style={{ width: 5, height: 50, backgroundColor: infoType == 'success' ? 'green' : 'red', position: 'absolute' }}></View>

                            </View>
                        </View> : null}
                    </View>
                </TouchableWithoutFeedback>

            </Modal>
        </SafeView>
    )
}

export default AddLefting