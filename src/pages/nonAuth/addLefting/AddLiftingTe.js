import React, { useRef, useState, useEffect, useCallback } from 'react'
import { ScrollView, Text, TextInput, View, Image, TouchableOpacity, FlatList, Modal, TouchableWithoutFeedback, Keyboard, Platform, KeyboardAvoidingView, Dimensions } from 'react-native'
import styles from './AddLeftingStyle'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import moment from 'moment'
import Toast from 'react-native-toast-message'
import { getApi, postApi, postApiWithHeader, getApiWithHeader, } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import OtpInputs from 'react-native-otp-inputs'
import Icon from 'react-native-vector-icons/AntDesign'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import ImagePath from '../../../image/ImagePath'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import DataStore from '../../../helper/constants/DataStore'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

var user_details

const AddLiftingTe = props => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const otpRef = useRef()

    const [isKeyboardVisible, setKeyboardVisible] = useState(false)
    const [keyboardHeight, setKeyboardHeight] = useState(0)
    const [loading, setLoading] = useState(false)
    const [isDatePickerVisible, setDatePickerVisibility] = useState(false)
    const [selectedDate, setSelectedDate] = useState(convertForShowData(moment(new Date()).format('DD-MM-YYYY')))
    const [qty, setQty] = useState('')
    const [remarks, setRemarks] = useState('')
    const [dealerRssdListCopy, setDealerRssdListCopy] = useState([])
    const [dealerRssdPhone, setDealerRssdPhone] = useState('')
    const [modalVisible, setModalVisible] = useState(false)
    const [liftingMinimumDate, setLiftingMinimumDate] = useState(2)
    const [otp, setOtp] = useState('')
    const [infoView, setInfoView] = useState(false)
    const [infoTxt, setInfoTxt] = useState('')
    const [inActive, setInActive] = useState(false)
    const [starSathibtn, setStarsathibtn] = useState(false)
    const [otpView, setOtpView] = useState(false)
    const [min, setMin] = useState('')
    const [max, setMax] = useState('')
    const [btnActive, setBtnActive] = useState(true)
    const [pickerPopup, setPickerPopup] = useState(false)
    const [dataSetList, setDataSetList] = useState([])
    const [searchDataSetList, setSearchDataSetList] = useState([])
    const [typePopup, setTypePopup] = useState(0)
    const [masonList, setMasonList] = useState([])
    const [valueMason, setValueMason] = useState('')
    const [labelMason, setLabelMason] = useState(textValue.Select_Mason)
    const [dealerRssdList, setDealerRssdList] = useState([])
    const [valueDealerRssd, setValueDealerRssd] = useState('')
    const [labelDealerRssd, setValueLabelRssd] = useState(textValue.Select_Dealer_Rssd_Sub_Dealer)
    const [products, setProducts] = useState([])
    const [value, setValue] = useState('')
    const [labelProduct, setLabelProduct] = useState(textValue.Select_Product)
    const [duplicateCheck, setDuplicateCheck] = useState(false)
    const [message1, setmessage1] = useState('')
    const [val1, setval1] = useState('')
    const [val2, setval2] = useState('')
    const [val3, setval3] = useState(0)
    const [searchText, setSearchText] = useState('')


    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setval3(0)
            getSettings()
            getData()
            get_mason_list(1, [])
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    useEffect(() => {
        const showSubscription = Keyboard.addListener('keyboardDidShow', (event) => {
            setKeyboardHeight(event.endCoordinates.height)
            setKeyboardVisible(true)
        })

        const hideSubscription = Keyboard.addListener('keyboardDidHide', () => {
            setKeyboardHeight(0)
            setKeyboardVisible(false)
        })

        return () => {
            showSubscription.remove()
            hideSubscription.remove()
        }
    }, [])

    useEffect(() => {
        if (typePopup == 1) {
            setDataSetList(masonList)
            const results = masonList.filter((item) =>
                item.label.toLowerCase().includes(searchText.toLowerCase())
            )
            setSearchDataSetList(results)
        }
    }, [masonList])

    useEffect(() => {
        if (typePopup == 2) {
            setDataSetList(dealerRssdList)
            const results = dealerRssdList.filter((item) =>
                item.label.toLowerCase().includes(searchText.toLowerCase())
            )
            setSearchDataSetList(results)
        }
    }, [dealerRssdList])

    useEffect(() => {
        if (typePopup == 3) {
            setDataSetList(products)
            const results = products.filter((item) =>
                item.label.toLowerCase().includes(searchText.toLowerCase())
            )
            setSearchDataSetList(results)
        }
    }, [products])

    const getSettings = async () => {
        getApiWithHeader(constants.app_registration_link_visible + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    if (response?.data?.data[0]?.lifting_send_star_sathi_app_button == '1') {
                        setStarsathibtn(true)
                    }
                    setMax(response?.data?.data[0]?.maximum_lifting_limit)
                    setMin(response?.data?.data[0]?.minimum_lifting_limit)
                }
            })
            .catch(err => { })
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
        } catch (e) { }
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
                    setLoading(false)
                    if (page_value == 1) { showToast('error', response.data.msg) }
                }
            })
            .catch(err => {
                setLoading(false)
            })
    }

    const get_all_dealers = async (val, page_value, value, value1) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        setLoading(true)

        let url = `get-all-dealers?mason_id=${val}&page=${page_value}` + '&preferred_app_lang=' + selectedLanguage()
        getApiWithHeader(url)
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
                        let obj = {
                            label: data[i]?.name,
                            value: data[i].id,
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
                    get_all_dealers(val, page_value + 1, a, b)
                } else {
                    if (response?.data?.status_code == 401) {
                        setLoading(true)
                        showToast('error', response?.data?.message)
                        _logout()
                    }
                }
            })
            .catch(err => {
                setLoading(true)
                setLoading(false)
            })
    }

    const lifting_date_disable_days_count = async id => {
        getApiWithHeader(constants.app_registration_link_visible + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    setLiftingMinimumDate(response?.data?.data[0]?.app_lifting_date)
                }
            })
            .catch(err => { })
    }

    const get_mason_list = async (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        getApiWithHeader(constants.te_mason_list + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {

                    let data = response.data.data.masonOptions
                    let masonListt = []
                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            label: convertForShowData(data[i].value),
                            value: data[i].key,
                        }
                        masonListt.push(obj)
                    }

                    var a = value
                    a = [...a, ...masonListt]

                    setMasonList(a)
                    get_mason_list(page_value + 1, a)
                } else {
                    setLoading(false)
                    if (response?.data?.status_code == 401) {
                        setLoading(false)
                        showToast('error', response?.data?.message)
                        _logout()
                    }
                }
            })
            .catch(err => {
                setLoading(false)
            })
    }

    const showDatePicker = () => {
        setDatePickerVisibility(true)
    }

    const hideDatePicker = () => {
        setDatePickerVisibility(false)
    }

    const handleConfirm = date => {
        setSelectedDate(moment(date).format('DD-MM-YYYY'))
        hideDatePicker()
    }

    const form_validation = () => {
        if (valueDealerRssd == null) {
            showToast('error', messageList.t30)
        } else if (value == null) {
            showToast('error', messageList.t31)
        } else if (qty == '') {
            showToast('error', messageList.t32)
        } else if (qty.includes('.')) {
            showToast('error', messageList.t33)
        } else if (selectedDate == '') {
            showToast('error', messageList.t34)
        } else {
            if (btnActive) {
                setBtnActive(false)
                if (parseInt(convertForUploadData(max)) > 0) {
                    if (parseInt(convertForUploadData(qty)) >= parseInt(convertForUploadData(min)) && parseInt(convertForUploadData(qty)) <= parseInt(convertForUploadData(max))) {
                        setLoading(true)
                        changeLanguage(0)
                        setInActive(false)
                    } else {
                        setBtnActive(true)
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
                        showToast('error', message,)
                    }
                } else {
                    setInActive(false)
                    setLoading(true)
                    changeLanguage(0)
                }
            }
        }
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
                check_add_lifting_data(val, result.data.translations[0].translatedText)
            })
            .catch(err => {
                check_add_lifting_data(val, arr[0])
            })
    }

    const check_add_lifting_data = async (val, remark) => {
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('mason_ids', '[' + valueMason + ']')
        formData.append('product_id', value)
        formData.append('qty', convertForUploadData(qty))
        formData.append('lifting_date', convertForUploadData(selectedDate))
        formData.append('remark', convertForUploadData(remark))
        formData.append('img', '')
        formData.append('dealer_rssd_id', valueDealerRssd)
        formData.append('req_type', val)
        formData.append('preferred_app_lang', selectedLanguage())

        postApiWithHeader(constants.check_duplicate_lifting, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    add_lifting(val, remark)
                } else {
                    setval1(val)
                    setval2(remark)
                    setmessage1(response.data.msg)
                    setDuplicateCheck(true)
                }
            })
            .catch(err => {
                setLoading(false)
            })
    }

    const add_lifting = async (val, remark) => {
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('mason_ids', '[' + valueMason + ']')
        formData.append('product_id', value)
        formData.append('qty', convertForUploadData(qty))
        formData.append('lifting_date', convertForUploadData(selectedDate))
        formData.append('remark', convertForUploadData(remark))
        formData.append('img', '')
        formData.append('dealer_rssd_id', valueDealerRssd)
        formData.append('req_type', 2)
        formData.append('preferred_app_lang', selectedLanguage())

        postApiWithHeader(constants.add_lifting, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    setBtnActive(true)
                    showToast('success', response.data.msg)
                    props.navigation.goBack()
                } else {
                    setBtnActive(true)
                    showToast('error', response.data.msg)
                    props.navigation.goBack()
                }
            })
            .catch(err => {
                setBtnActive(true)
                setLoading(false)
            })
    }

    const add_lifting1 = async (val, remark) => {
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('mason_ids', '[' + valueMason + ']')
        formData.append('product_id', value)
        formData.append('qty', convertForUploadData(qty))
        formData.append('lifting_date', convertForUploadData(selectedDate))
        formData.append('remark', convertForUploadData(remark))
        formData.append('img', '')
        formData.append('dealer_rssd_id', valueDealerRssd)
        formData.append('req_type', 2)
        formData.append('preferred_app_lang', selectedLanguage())

        postApiWithHeader(constants.add_lifting, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    setBtnActive(true)
                    showToast('success', response.data.msg)
                    props.navigation.goBack()
                } else {
                    setBtnActive(true)
                    showToast('error', response.data.msg)
                    props.navigation.goBack()
                }
            })
            .catch(err => {
                setBtnActive(true)
                setLoading(false)
            })
    }

    const showToast = (type, msg) => {
        Toast.show({
            type: type,
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
                    setInfoTxt(response.data.msg)
                    infoType = 'success'
                    setInfoView(true)
                    setTimeout(() => {
                        setInfoView(false)
                    }, 2000)
                } else {
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
                setInfoTxt(messageList.t4)
                infoType = 'error'
                setInfoView(true)
                setTimeout(() => {
                    setInfoView(false)
                }, 2000)
            })
    }

    const verify_otp = async val => {
        if (!inActive) {
            setLoading(true)
            var FormData = require('form-data')
            var formData = new FormData()
            formData.append('phone', convertForUploadData(dealerRssdPhone))
            formData.append('otp', convertForUploadData(otp))
            formData.append('preferred_app_lang', selectedLanguage())
            setInActive(true)
            postApi(constants.verify_phone, formData)
                .then(response => {
                    setLoading(false)
                    if (response.data.status) {
                        setModalVisible(false)
                        setLoading(true)
                        changeLanguage(1)
                    } else {
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
            const keys = ['user_info', 'access_token']
            await AsyncStorage.multiRemove(keys)
            props.navigation.reset({
                index: 0,
                routes: [{ name: 'AuthStack' }],
            })
        } catch (e) { }
    }

    const selectPopupValue = (item) => {
        if (typePopup == 1) {
            setValueMason(item.value)
            setLabelMason(item.label)
            setValueLabelRssd(textValue.Select_Dealer_Rssd_Sub_Dealer)
            setLabelProduct(textValue.Select_Product)
            setPickerPopup(false)
            setSearchText('')
            setval3(1)
            get_all_dealers(item.value, 1, [], [])
        }
        if (typePopup == 2) {
            setValueDealerRssd(item.value)
            setValueLabelRssd(item.label)
            setLabelProduct(textValue.Select_Product)
            setPickerPopup(false)
            setSearchText('')
            for (var i = 0; i < dealerRssdListCopy.length; i++) {
                if (item.value == dealerRssdListCopy[i].id) {
                    setDealerRssdPhone(dealerRssdListCopy[i].phone)
                }
            }
        }
        if (typePopup == 3) {
            setValue(item.value)
            setLabelProduct(item.label)
            setPickerPopup(false)
            setSearchText('')
        }

    }

    const searchDealer = (text) => {
        const results = dataSetList.filter((item) =>
            item.label.toLowerCase().includes(text.toLowerCase())
        )
        setSearchText(text)
        setSearchDataSetList(results)
    }

    const renderPopupItem = ({ item, index }) => {
        return (
            <TouchableOpacity onPress={() => { selectPopupValue(item) }}>
                <View style={{ width: '100%', height: 40, paddingHorizontal: 10, alignItems: 'center', justifyContent: 'center', flexDirection: 'row', backgroundColor: '#f9f9f9', marginVertical: 5, borderRadius: 5 }}>
                    <Text style={{ flex: 1, color: '#000', fontSize: 14 }}>{convertForShowData(item?.label)}</Text>
                </View>
            </TouchableOpacity>
        )
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={{ width: '100%', height: '100%', flexDirection: 'column', backgroundColor: '#FFF' }}>
                <View style={{ width: '100%', height: 100, borderBottomLeftRadius: 25, borderBottomRightRadius: 25, backgroundColor: '#EE1D23' }} />
            </View>

            <View style={{ width: '100%', height: '100%', position: 'absolute', flexDirection: 'column' }}>
                <View style={{ height: Platform.OS == 'ios' ? 25 : 0 }} />
                <View style={{ width: '100%', height: 70 }}>
                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{convertForShowData(textValue.MASON_SALES_ENTRY)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {setTimeout(()=>{
                                props.navigation.goBack()
                            },500) }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{ flex: 1 }}>
                    <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                        <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                            <ScrollView showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                                <View style={{ flexDirection: 'column', width: '100%' }}>
                                    <>
                                        <TouchableOpacity onPress={() => {
                                            setTypePopup(1)
                                            setDataSetList(masonList)
                                            setSearchDataSetList(masonList)
                                            setPickerPopup(true)
                                        }} style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{ flex: 1, color: '#000' }}>{convertForShowData(labelMason)}</Text>
                                            <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                                            <View style={{ width: 10 }} />
                                        </TouchableOpacity>
                                    </>
                                    <View style={{ height: 10 }} />
                                    {user_details?.data?.role == 3 || user_details?.data?.role == 4 ? null : <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <TouchableOpacity onPress={() => {
                                            if (val3 == 0) {
                                                showToast('error', messageList.t41)
                                            } else {
                                                setTypePopup(2)
                                                setDataSetList(dealerRssdList)
                                                setSearchDataSetList(dealerRssdList)
                                                setPickerPopup(true)
                                            }
                                        }} style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{ flex: 1, color: '#000' }}>{convertForShowData(labelDealerRssd)}</Text>
                                            <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                                            <View style={{ width: 10 }} />
                                        </TouchableOpacity>
                                        <View style={{ height: 10 }} />
                                    </View>}
                                    <>
                                        <TouchableOpacity onPress={() => {
                                            setTypePopup(3)
                                            setDataSetList(products)
                                            setSearchDataSetList(products)
                                            setPickerPopup(true)
                                        }} style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{ flex: 1, color: '#000' }}>{convertForShowData(labelProduct)}</Text>
                                            <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                                            <View style={{ width: 10 }} />
                                        </TouchableOpacity>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                style={{ width: '100%' }}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.Enter_Qty_No_Of_Bags}
                                                onChangeText={text => setQty(text)}
                                                value={convertForShowData(qty)}
                                                keyboardType='number-pad'
                                                color='#000000' />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{ width: '100%', color: '#000000', }}>{convertForShowData(selectedDate)}</Text>
                                            <TouchableOpacity onPress={() => { showDatePicker() }} style={styles._lowerView._scrollView._dob}>
                                                <Image style={styles._lowerView._scrollView._dob._img} source={Icons.calender} />
                                            </TouchableOpacity>
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 120, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD' }}>
                                            <TextInput
                                                multiline={true}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.Remarks}
                                                onChangeText={text => setRemarks(text)}
                                                value={convertForShowData(remarks)}
                                                color='#000000' />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <TouchableOpacity onPress={() => {
                                            setOtpView(false)
                                            form_validation()
                                        }} style={styles._lowerView._btn}>
                                            <Text style={styles._lowerView._btn._txt}>{convertForShowData(textValue.Submit)}</Text>
                                        </TouchableOpacity>
                                    </>
                                </View>
                            </ScrollView>
                        </View>
                    </View>
                </KeyboardAvoidingView>
            </View>

            <DateTimePickerModal
                isVisible={isDatePickerVisible}
                mode='date'
                onConfirm={handleConfirm}
                onCancel={hideDatePicker}
                maximumDate={new Date()}
                minimumDate={new Date(moment().subtract(liftingMinimumDate, 'days'))}
                date={new Date()}
            />
            {loading ? <Loader /> : null}

            <Modal
                animationType='fade'
                transparent={true}
                visible={modalVisible}
                onRequestClose={res => { setModalVisible(!modalVisible) }}>
                <TouchableWithoutFeedback>
                    <View style={{ height: '100%', width: '100%', backgroundColor: '#00000095', justifyContent: 'center', alignItems: 'center', position: 'relative', }}>
                        <View style={{ width: '90%', backgroundColor: '#fff', borderRadius: 10, alignItems: 'center', padding: 10, }}>
                            {otpView === true ? (
                                <Text style={{ fontSize: 18, fontWeight: '700' }}>{convertForShowData(textValue.ENTER_OTP)}</Text>
                            ) : null}
                            {otpView === true ? (
                                <View style={{ width: '70%', height: 50, marginTop: '2%', marginLeft: 10, }}>
                                    <OtpInputs
                                        clearTextOnFocus
                                        handleChange={handleChange}
                                        keyboardType='phone-pad'
                                        numberOfInputs={4}
                                        ref={otpRef}
                                        selectTextOnFocus={false}
                                        inputStyles={{ borderColor: '#a8a8a8', borderWidth: 1, borderRadius: 5, textAlign: 'center', fontSize: 18, fontWeight: '600', width: 45, height: 45, backgroundColor: '#fff', color: '#000', }} />
                                </View>
                            ) : null}
                            {otpView === true ? (
                                <View style={styles._btn_section}>
                                    <Text style={styles._btn_section._txt}>
                                        {convertForShowData(textValue.Didnt_receive_an_OTP)}
                                    </Text>
                                    <TouchableOpacity activeOpacity={0.8} onPress={() => { send_otp() }}>
                                        <Text style={styles._btn_section._resend_btn}>
                                            {convertForShowData(textValue.Resend_OTP)}
                                        </Text>
                                    </TouchableOpacity>
                                </View>
                            ) : null}
                            {otpView === true ? (
                                <TouchableOpacity onPress={() => { verify_otp() }} style={styles._lowerView._btn} activeOpacity={0.8}>
                                    <Text style={styles._lowerView._btn._txt}>{convertForShowData(textValue.Verify)}</Text>
                                </TouchableOpacity>
                            ) : null}

                            {otpView === false ? (
                                <TouchableOpacity onPress={() => {
                                    setOtpView(true)
                                    send_otp()
                                }} style={styles._lowerView._btn} activeOpacity={0.8}>
                                    <Text style={styles._lowerView._btn._txt}>
                                        {convertForShowData(textValue.Verify_from_OTP)}
                                    </Text>
                                </TouchableOpacity>
                            ) : null}
                            {starSathibtn == true && otpView == false ? (
                                <View style={{ width: '100%', justifyContent: 'center', alignItems: 'center', paddingTop: 4, }}>
                                    <Text>{convertForShowData(textValue.Or)}</Text>
                                </View>
                            ) : null}
                            {starSathibtn == true && otpView == false ? (
                                <TouchableOpacity onPress={() => {
                                    setLoading(true)
                                    changeLanguage(2)
                                }} style={styles._lowerView._btn} activeOpacity={0.8}>
                                    <Text style={styles._lowerView._btn._txt}>
                                        {convertForShowData(textValue.Verify_from_Star_Saathi)}
                                    </Text>
                                </TouchableOpacity>
                            ) : null}
                            <TouchableOpacity activeOpacity={0.8} style={{ height: 25, width: 25, justifyContent: 'center', alignItems: 'center', position: 'absolute', right: 2, top: 2, backgroundColor: '#fff', borderRadius: 5, }} onPress={() => { setModalVisible(false) }}>
                                <Icon name='closecircle' size={20} color='#ee1d23' />
                            </TouchableOpacity>
                        </View>
                        {loading ? <Loader /> : null}
                        {infoView ? (
                            <View style={{ width: '100%', alignItems: 'center', position: 'absolute', top: '10%', }}>
                                <View style={{ width: '90%', height: 50, backgroundColor: '#fff', justifyContent: 'center', padding: 10, shadowColor: '#000000', shadowOffset: { width: 0, height: 3, }, shadowRadius: 5, shadowOpacity: 1.0, elevation: 5, borderRadius: 8, overflow: 'hidden', }}>
                                    <Text numberOfLines={2} style={{ fontSize: 12, fontWeight: '600', marginLeft: 5 }}>
                                        {convertForShowData(infoTxt)}
                                    </Text>
                                    <View style={{ width: 5, height: 50, backgroundColor: infoType == 'success' ? 'green' : 'red', position: 'absolute', }} />
                                </View>
                            </View>
                        ) : null}
                    </View>
                </TouchableWithoutFeedback>
            </Modal>

            <Modal
                animationType='fade'
                transparent={true}
                visible={duplicateCheck}
                onRequestClose={(res) => {
                    setBtnActive(true)
                    setDuplicateCheck(!duplicateCheck)
                }}>
                <TouchableWithoutFeedback>
                    <View style={{ height: '100%', width: '100%', backgroundColor: '#00000095', justifyContent: 'center', alignItems: 'center', position: 'relative' }}>
                        <View style={{ width: '80%', padding: 20, backgroundColor: '#FFF', borderRadius: 10 }}>
                            <Text style={{ fontSize: 16, fontWeight: '700', color: '#000' }}>{convertForShowData(textValue.Save)}...</Text>
                            <View style={{ height: 10 }} />
                            <Text style={{ fontSize: 14, color: '#444' }}>{message1}</Text>
                            <View style={{ height: 10 }} />
                            <View style={{ width: '100%', flexDirection: 'row' }}>
                                <View style={{ width: 10 }} />
                                <TouchableOpacity onPress={() => {
                                    setBtnActive(true)
                                    setDuplicateCheck(!duplicateCheck)
                                }} style={{ flex: 1, alignItems: 'center', justifyContent: 'center', height: 40, backgroundColor: '#eee', borderRadius: 5 }}>
                                    <Text style={{ fontSize: 16, fontWeight: '700', color: '#000' }}>{textValue.Cancel}</Text>
                                </TouchableOpacity>
                                <View style={{ width: 30 }} />
                                <TouchableOpacity onPress={() => {
                                    setDuplicateCheck(!duplicateCheck)
                                    add_lifting1(val1, val2)
                                }} style={{ flex: 1, alignItems: 'center', justifyContent: 'center', height: 40, backgroundColor: '#EE1D23', borderRadius: 5 }}>
                                    <Text style={{ fontSize: 16, fontWeight: '700', color: '#fff' }}>{textValue.Save}</Text>
                                </TouchableOpacity>
                                <View style={{ width: 10 }} />
                            </View>
                        </View>
                    </View>
                </TouchableWithoutFeedback>

            </Modal>


            {pickerPopup ? <View style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0006' }}>
                <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                    <TouchableOpacity onPress={() => setPickerPopup(false)} style={{ minHeight: 120, flex: 1 }} />
                    <View style={{ width: '100%', minHeight: isKeyboardVisible ? 200 + keyboardHeight : 200, paddingHorizontal: 20, paddingTop: 20, flexDirection: 'column', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                        <View style={{ width: '100%', backgroundColor: '#FFE8E9', padding: 15, borderTopLeftRadius: 10, borderTopRightRadius: 10 }}>
                            <View style={{ width: '100%', height: 45, backgroundColor: '#FFF', borderRadius: 10, paddingHorizontal: 20, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                <TextInput style={{ flex: 1, height: '100%', color: '#000' }} placeholder={textValue.Search} placeholderTextColor={'#bbb'} onChangeText={searchDealer} value={searchText} />
                                <View style={{ width: 1, height: '70%', backgroundColor: '#FFE8E9' }} />
                                <View style={{ width: 15 }} />
                                <Image style={{ width: 20, height: 20, tintColor: '#000' }} source={Icons.search} />
                            </View>
                            <View style={{ height: 10 }} />
                            <FlatList
                                style={{ maxHeight: Dimensions.get('screen').height - 400, minHeight: 200 }}
                                data={searchDataSetList}
                                renderItem={renderPopupItem}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={(item, index) => `${item}-${index}`}
                            />
                        </View>
                    </View>
                </View>
            </View> : null}
        </SafeView>
    )
}

export default AddLiftingTe
