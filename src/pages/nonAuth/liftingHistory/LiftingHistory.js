import React, { useState, useEffect, useRef, useCallback } from 'react'
import { Text, TextInput, View, Image, TouchableOpacity, Modal, Platform, ActivityIndicator, FlatList, } from 'react-native'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import moment from 'moment'
import styles from './LiftingHistoryStyle'
import { getApi, postApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import { searchItem } from '../../../helper/search/SearchItem'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import DataStore from '../../../helper/constants/DataStore'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

var user_details
var _editable_obj

const LiftingHistory = props => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [months, setMonths] = useState([])
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
    const [products, setProducts] = useState('')
    const [qty, setQty] = useState('')
    const [remark, setRemark] = useState('')
    const [error, setError] = useState(false)
    const [errorTxt, setErrorTxt] = useState('')

    const [pickerPopup, setPickerPopup] = useState(false)
    const [dataSetList, setDataSetList] = useState([])
    const [typePopup, setTypePopup] = useState(0)
    const [value, setValue] = useState('')
    const [label, setLabel] = useState(textValue.Select_Product)
    const [valueYr, setValueYr] = useState('')
    const [labelYr, setLabelYr] = useState(textValue.Year)
    const [valueMnt, setValueMnt] = useState('')
    const [labelMnt, setLabelMnt] = useState(textValue.Month)


    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setTimeout(()=>{
                getData()
            },500)
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

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
    }, [isFocusRef.current])

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
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        //console.log('Focus state:    asasasasa', isFocusRef.current)

        var formData = new FormData()
        formData.append('id', user_details?.data?.id)
        formData.append('role', user_details?.data?.role)
        formData.append('preferred_app_lang', selectedLanguage())
        //console.log(constants.get_lifting_history + `?page=${page_value}&preferred_app_lang=` + selectedLanguage())

        await postApiWithHeader(constants.get_lifting_history + `?page=${page_value}&preferred_app_lang=` + selectedLanguage(), formData)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
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
                update_lifting(arr[0])
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
                    if (page_value == 1) { showToast('error', response.data.msg) }
                }
            })
            .catch(err => {
                setLoading(false)
            })
    }

    const filder_by_dropdown = async (monthValue, yearValue) => {
        let array_list = []
        setSearchTxt('')
        if (monthValue && yearValue) {
            for (var i = 0; i < copyLiftingHistory.length; i++) {
                let month = moment(copyLiftingHistory[i].lifting_date, 'DD-MM-YYYY').format('M')
                let year = moment(copyLiftingHistory[i].lifting_date, 'DD-MM-YYYY').format('YYYY')
                if (month == monthValue && year == yearValue) {
                    array_list.push(copyLiftingHistory[i])
                }
            }
            setLiftingHistory(array_list)
        } else if (yearValue) {
            for (var i = 0; i < copyLiftingHistory.length; i++) {
                let year = moment(copyLiftingHistory[i].lifting_date, 'DD-MM-YYYY').format('YYYY')
                if (year == yearValue) {
                    array_list.push(copyLiftingHistory[i])
                }
            }
            setLiftingHistory(array_list)
        } else {
            for (var i = 0; i < copyLiftingHistory.length; i++) {
                let month = moment(copyLiftingHistory[i].lifting_date, 'DD-MM-YYYY').format('M')

                if (month == monthValue) {
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

    const selectPopupValue = (item) => {
        if (typePopup == 0) {
            setLabelMnt(item.label)
            setValueMnt(item.value)
            setPickerPopup(false)
            filder_by_dropdown(item.value, valueYr)
        }
        if (typePopup == 1) {
            setLabelYr(item.label)
            setValueYr(item.value)
            setPickerPopup(false)
            filder_by_dropdown(valueMnt, item.value)
        }
        if (typePopup == 2) {
            setValue(item.value)
            setLabel(item.label)
            setPickerPopup(false)
            setError(false)
            setErrorTxt('')
        }
    }

    const renderItem = ({ item }) => (
        <View style={{ width: '100%', justifyContent: 'center', alignItems: 'center' }}>
            {user_details?.data?.role != 2 ? (
                <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', padding: 10, borderRadius: 10, backgroundColor: '#FFDFE1' }}>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.transaction_id)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.transaction_id)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.MASON)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.mason_name)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.PRODUCTS)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.product_name)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.NO_OF_BAGS)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.qty)}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.LIFTING_DATE)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.lifting_date)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {item?.is_verified === 1 ? convertForShowData(item?.point) : convertForShowData(textValue.In_progress)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DEALER)}/{convertForShowData(textValue.RSSD)}/{convertForShowData(textValue.SUB_DEALER)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.dealer_name)}
                            </Text>
                        </View>
                    </View>
                    {item?.request_send_to === 2 ? <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.STAR_SAATHI_STATUS)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {item?.star_sathi_status === 0 ? convertForShowData(textValue.Pending) : (item?.star_sathi_status === 1 ? convertForShowData(textValue.Approved) : convertForShowData(textValue.Rejected))}
                            </Text>
                        </View>
                    </View> : null}
                    {item?.request_send_to === 2 && item?.action_taken_at !== null ? <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.APPROVED_REJECTED_DATE_AND_TIME)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item.action_taken_at)}
                            </Text>
                        </View>
                    </View> : null}
                </View>
            ) : (
                <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', padding: 10, borderRadius: 5, backgroundColor: '#FFDFE1' }}>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.transaction_id)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.transaction_id)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.PRODUCTS)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.product_name)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.NO_OF_BAGS)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.qty)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.LIFTING_DATE)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.lifting_date)}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.LIFTING_DATE)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.lifting_date)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {item?.is_verified === 1 ? convertForShowData(item?.point) : convertForShowData(textValue.In_progress)}
                            </Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DEALER)}/{convertForShowData(textValue.RSSD)}/{convertForShowData(textValue.SUB_DEALER)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item?.dealer_name)}
                            </Text>
                        </View>
                    </View>
                    {item?.request_send_to === 2 ? <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.STAR_SAATHI_STATUS)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {item?.star_sathi_status === 0 ? convertForShowData(textValue.Pending) : (item?.star_sathi_status === 1 ? convertForShowData(textValue.Approved) : convertForShowData(textValue.Rejected))}
                            </Text>
                        </View>
                    </View> : null}
                    {item?.request_send_to === 2 && item?.action_taken_at !== null ? <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, paddingLeft: 10, paddingRight: 5, paddingVertical: 5, marginTop: 5, backgroundColor: '#fff', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.APPROVED_REJECTED_DATE_AND_TIME)}</Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>
                                {convertForShowData(item.action_taken_at)}
                            </Text>
                        </View>
                    </View> : null}
                </View>
            )}
        </View>
    )

    const renderFooter = () => {
        if (!flatlistLoader) return null
        return (
            <View style={{ paddingHorizontal: 20, paddingTop: 20, alignItems: 'center', }}>
                <ActivityIndicator size='large' color='#ee1d23' />
            </View>
        )
    }

    const renderPopupItem = ({ item }) => {
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
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{convertForShowData(textValue.LIFTING_HISTORY)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {
                            setTimeout(()=>{
                                props.navigation.goBack()
                            },500)
                        }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                        <View style={{ width: '100%', height: 45, backgroundColor: '#FFF5F6', borderRadius: 10, paddingHorizontal: 20, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                            <TextInput
                                style={{ flex: 1, height: '100%', color: '#000' }}
                                placeholder={textValue.Search_Name + '/' + textValue.Mobile_No + '/' + textValue.Aadhar}
                                value={convertForShowData(searchTxt)}
                                onChangeText={text => {
                                    setSearchTxt(text)

    // ✅ Empty search → restore full list
    if (text.trim().length === 0) {
        setLiftingHistory(copyLiftingHistory)
        setFlatListLoader(false)
        return
    }

    const searchText = text.toLowerCase()

    const filteredData = copyLiftingHistory.filter(item =>
        item?.mason_name
            ?.toLowerCase()
            .includes(searchText)
    )

    setLiftingHistory(filteredData)

    // ✅ local search → no loader
    setFlatListLoader(false)
                                }} />
                            <View style={{ width: 1, height: '70%', backgroundColor: '#FFD5D6' }} />
                            <View style={{ width: 15 }} />
                            <Image style={{ width: 20, height: 20, tintColor: '#000' }} source={Icons.search} />
                        </View>
                        <View style={{ height: 10 }} />
                        <View style={{ width: '100%', flexDirection: 'row' }}>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                onPress={() => {
                                    setTypePopup(0)
                                    setDataSetList(months)
                                    setPickerPopup(true)
                                }}
                                style={{ flex: 1, height: 45, borderRadius: 10, borderColor: '#FFD5D6', borderWidth: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 10, flexDirection: 'row' }}>
                                <Text style={{ flex: 1, color: '#000' }}>{convertForShowData(labelMnt)}</Text>
                                <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 15, resizeMode: 'contain' }} />
                            </TouchableOpacity>
                            <View style={{ width: 5 }} />
                            <TouchableOpacity
                                activeOpacity={0.8}
                                onPress={() => {
                                    setTypePopup(1)
                                    setDataSetList(yrs)
                                    setPickerPopup(true)
                                }}
                                style={{ flex: 1, height: 45, borderRadius: 10, borderColor: '#FFD5D6', borderWidth: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 10, flexDirection: 'row' }}>
                                <Text style={{ flex: 1, color: '#000' }}>{convertForShowData(labelYr)}</Text>
                                <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 15, resizeMode: 'contain' }} />
                            </TouchableOpacity>
                            <View style={{ width: 5 }} />
                            <TouchableOpacity
                                activeOpacity={0.8}
                                onPress={() => {
                                    setValueMnt('')
                                    setLabelMnt(textValue.Month)
                                    setValueYr('')
                                    setLabelYr(textValue.Year)
                                    getData()
                                }}
                                style={{ width: 45, height: 45, borderRadius: 10, backgroundColor: '#FFF2F2', alignItems: 'center', justifyContent: 'center' }}>
                                <Image source={ImagePath.ReloadIcon} style={{ height: 20, width: 20, resizeMode: 'contain' }} />
                            </TouchableOpacity>
                        </View>
                        <View style={{ height: 20 }} />
                        {liftingHistory?.length > 0 ?( <FlatList
                            data={liftingHistory}
                            renderItem={renderItem}
                            ItemSeparatorComponent={() => <View style={{ height: 15 }} />}
                            showsHorizontalScrollIndicator={false}
                            showsVerticalScrollIndicator={false}
                            ListFooterComponent={renderFooter}
                            keyExtractor={(item, index) => `${item}-${index}`}
                        />): (<View style={{flex: 1,alignItems: 'center', justifyContent: 'center'}}>
                            <Text style={{color: "#000", fontSize: 16}}>No Data</Text>
                        </View>)}
                    </View>
                </View>
            </View>

            <Modal
                animationType='fade'
                transparent={true}
                visible={modalVisible}
                onRequestClose={res => {
                    setModalVisible(!modalVisible)
                }}>
                <View style={styles._modal_view}>
                    <View style={[styles._modal_view._view, { height: open === true ? 300 : 300 },]}>
                        <Text style={styles._modal_view._view._header}>
                            {convertForShowData(textValue.UPDATE_LIFTING_DETAILS)}
                        </Text>

                        <View style={styles._modal_view._input_section}>
                            <TouchableOpacity
                                onPress={() => {
                                    setTypePopup(2)
                                    setDataSetList(products)
                                    setPickerPopup(true)
                                }}
                                style={{ width: '100%', height: 45, borderRadius: 10, borderColor: '#FFD5D6', borderWidth: 1, alignItems: 'center', justifyContent: 'center', flexDirection: 'row', paddingHorizontal: 15 }}>
                                <Text style={{ flex: 1, color: '#000' }}>{convertForShowData(label)}</Text>
                                <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                            </TouchableOpacity>
                            <View style={{ flex: 1 }} />
                            <View style={{ width: '100%', height: 45, borderRadius: 10, borderColor: '#FFD5D6', borderWidth: 1, alignItems: 'center', justifyContent: 'center' }}>
                                <TextInput
                                    style={{ width: '100%', paddingHorizontal: 15, color: '#000' }}
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
                            <View style={{ flex: 1 }} />
                            {error ? (
                                <View style={styles._modal_view._error_txt}>
                                    <Text style={styles._modal_view._error_txt._txt}>
                                        {convertForShowData(errorTxt)}
                                    </Text>
                                </View>
                            ) : null}
                            <View style={{ width: '100%', height: 45, borderRadius: 10, borderColor: '#FFD5D6', borderWidth: 1, alignItems: 'center', justifyContent: 'center' }}>
                                <TextInput
                                    style={{ width: '100%', paddingHorizontal: 15, color: '#000' }}
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Remarks}
                                    onChangeText={text => {
                                        setRemark(text)
                                    }}
                                    value={convertForShowData(remark)}
                                />
                            </View>
                            <View style={{ flex: 1 }} />
                        </View>

                        <View style={styles._modal_view._btn_section}>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={styles._modal_view._btn_section._touchableOpacity}
                                onPress={() => setModalVisible(false)}>
                                <Text style={styles._modal_view._btn_section._touchableOpacity._txt}>
                                    {convertForShowData(textValue.CANCEL)}
                                </Text>
                            </TouchableOpacity>
                            <TouchableOpacity activeOpacity={0.8} style={styles._modal_view._btn_section._touchableOpacity} onPress={() => { form_validate() }}>
                                <Text style={styles._modal_view._btn_section._touchableOpacity._txt}>
                                    {convertForShowData(textValue.UPDATE)}
                                </Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>

                {loading ? <Loader /> : null}
            </Modal>
            {pickerPopup ? <View style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0006' }}>
                <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                    <TouchableOpacity onPress={() => setPickerPopup(false)} style={{ minHeight: 120, flex: 1 }} />
                    <View style={{ width: '100%', paddingHorizontal: 20, paddingTop: 20, flexDirection: 'column', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                        <View style={{ width: '100%', backgroundColor: '#FFE8E9', padding: 15, borderTopLeftRadius: 10, borderTopRightRadius: 10 }}>
                            <FlatList
                                data={dataSetList}
                                renderItem={renderPopupItem}
                                keyExtractor={item => item.id}
                            />
                        </View>
                    </View>
                </View>
            </View> : null}
        </SafeView>
    )
}

export default LiftingHistory
