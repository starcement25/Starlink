import React, { useCallback, useRef, useState } from 'react'
import { Text, View, Image, TouchableOpacity, Modal, Platform, } from 'react-native'
import styles from './OrderEnquiryListStyle'
import { FlashList } from '@shopify/flash-list'
import moment from 'moment'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import { getApiWithHeader, postApiWithHeader } from '../../../helper/http/Api'
import { FlatList } from 'react-native-gesture-handler'
import Constants from '../../../helper/constants/Constants'
import Toast from 'react-native-toast-message'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import DataStore from '../../../helper/constants/DataStore'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

const OrderEnquiryList = props => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [fromDate, setFromDate] = useState('')
    const [toDate, setToDate] = useState('')
    const [isFromDate, setIsFromDate] = useState(false)
    const [isToDate, setIsToDate] = useState(false)
    const [listData, setListData] = useState([])
    const [flagEdit, setFlagEdit] = useState('pending')
    const [modalVisible, setModalVisible] = useState(false)
    const [selectedItem, setSlectedItem] = useState('')

    const [isPending, setIsPending] = useState(true)
    const [isApproved, setIsApproved] = useState(false)
    const [isReject, setIsReject] = useState(false)

    const [masonList, setMasonList] = useState([])
    const [value, setValue] = useState('')
    const [label, setLabel] = useState('')
    const [pickerPopup, setPickerPopup] = useState(false)
    const [dataSetList, setDataSetList] = useState([])
    const [typePopup, setTypePopup] = useState(0)

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setLoading(true)
            DataStore.value = 'Pending'
            getLiftingPending(1, [])
            callSearchApi('34646', 1, [])
            getMasonList(0)
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    const hideDatePickerFrom = () => {
        setIsFromDate(false)
    }

    const getMasonList = (type) => {
        let url = `te/starsaathi/get-mason-options?req_status=${type}&preferred_app_lang=` + selectedLanguage()
        getApiWithHeader(url)
            .then(response => {
                if (response.data) {
                    let products = []
                    for (let i = 0; i < response.data.data.masonOptions.length; i++) {
                        let obj = {
                            label: response.data.data.masonOptions[i].value,
                            value: response.data.data.masonOptions[i].key
                        }
                        products.push(obj)
                    }
                    setMasonList(products)
                }
            })
            .catch(err => {
                setLoading(false)
                
            })
    }

    const handleConfirmFrom = date => {
        setFromDate(moment(date).format('YYYY-MM-DD'))
        console.warn('A date has been picked: ', date)
        if (flagEdit == 'pending') {
            getFilteredList('pending', date, '', 1, [])
        } else if (flagEdit == 'approved') {
            getFilteredList('approved', date, '', 1, [])
        } else if (flagEdit == 'rejected') {
            getFilteredList('rejected', date, '', 1, [])
        }
        hideDatePickerFrom()
    }

    const hideDatePickerTo = () => {
        setIsToDate(false)
    }

    const handleConfirmTo = date => {
        setToDate(moment(date).format('YYYY-MM-DD'))
        console.warn('A date has been picked: ', date)
        if (flagEdit == 'pending') {
            getFilteredList('pending', '', date, 1, [])
        } else if (flagEdit == 'approved') {
            getFilteredList('approved', '', date, 1, [])
        } else if (flagEdit == 'rejected') {
            getFilteredList('rejected', '', date, 1, [])
        }
        hideDatePickerTo()
    }

    const getLiftingPending = (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        if (page_value == 1) {
            setIsPending(true)
            setIsApproved(false)
            setIsReject(false)
            setLoading(true)
            setListData([])
        }
        let url = `te/starsaathi/get-pending-liftings?page=${page_value}&preferred_app_lang=` + selectedLanguage()
        getApiWithHeader(url)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
                    setLoading(false)
                    setFlagEdit('pending')
                    var a = value
                    a = [...a, ...response.data.data.pendingLists]

                    if (DataStore.value == 'Pending') {
                        setListData(a)
                        getLiftingPending(page_value + 1, a)
                    }
                } else {
                    setLoading(false)
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else if (page_value == 1) {
                        showToast('error', response?.data?.msg)
                    }
                }
            })
            .catch(err => {
                setLoading(false)
                
            })
    }

    const getLiftingApproved = (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        if (page_value == 1) {
            setIsPending(false)
            setIsApproved(true)
            setIsReject(false)
            setLoading(true)
            setListData([])
        }
        let url = `te/starsaathi/get-accept-liftings?page=${page_value}&preferred_app_lang=` + selectedLanguage()
        getApiWithHeader(url)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data) {
                    setLoading(false)
                    setFlagEdit('approved')
                    var a = value
                    a = [...a, ...response.data.data.acceptedLists]

                    if (page_value == 1) {
                        getMasonList(1)
                    }
                    if (DataStore.value == 'Approved') {

                        setListData(a)
                        getLiftingApproved(page_value + 1, a)
                    }
                } else {
                    setLoading(false)
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else if (page_value == 1) {
                        showToast('error', response?.data?.msg)
                    }
                }
            })
            .catch(err => {
                setLoading(false)
                
            })
    }

    const getRejectedLifting = (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        if (page_value == 1) {
            setIsPending(false)
            setIsApproved(false)
            setIsReject(true)
            setLoading(true)
            setListData([])
        }
        let url = `te/starsaathi/get-reject-liftings?page=${page_value}&preferred_app_lang=` + selectedLanguage()
        getApiWithHeader(url)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data) {
                    setLoading(false)
                    setFlagEdit('rejected')
                    var a = value
                    a = [...a, ...response.data.data.rejectedLists]

                    if (page_value == 1) {
                        getMasonList(2)
                    }
                    if (DataStore.value == 'Reject') {
                        setListData(a)
                        getRejectedLifting(page_value + 1, a)
                    }
                } else {
                    setLoading(false)
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else if (page_value == 1) {
                        showToast('error', response?.data?.msg)
                    }
                }
            })
            .catch(err => {
                setLoading(false)
                
            })
    }

    const callAcceptApi = item => {
        let newwData = item.dataItem
        setModalVisible(false)
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('lifting_id', newwData[0].value)
        formData.append('preferred_app_lang', selectedLanguage())
        postApiWithHeader(Constants.accept_lifting, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    Toast.show({
                        type: 'success',
                        text2: response.data.msg,
                        text2NumberOfLines: 2
                    })
                    getLiftingPending(1, [])
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
            text2NumberOfLines: 2
        })
    }

    const getFilteredList = (type, date, todate, page, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        let url
        if (page == 1) {
            setLoading(true)
            setListData([])
        }
        if (type == 'pending') {
            if (date != '' && todate == '') {
                url = `te/starsaathi/get-pending-liftings?page=${page}&fromDate=${moment(date).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            } else if (todate != '' && date == '') {
                url = `te/starsaathi/get-pending-liftings?page=${page}&toDate=${moment(todate).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            } else if (date !== '' && todate !== '') {
                url = `te/starsaathi/get-pending-liftings?page=${page}&fromDate=${moment(date).format('YYYY-MM-DD')}&toDate=${moment(todate).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            }
            getApiWithHeader(url)
                .then(response => {
                    if (!isFocusRef.current) {
                        //console.log('⛔ Skipping API because screen not focused')
                        return
                    }
                    if (response.data) {
                        setLoading(false)
                        setFlagEdit(type)
                        var a = value
                        a = [...a, ...response.data.data.pendingLists]

                        setListData(a)
                    }
                })
                .catch(err => {
                    setLoading(false)
                    
                })
        }
        if (type == 'approved') {
            if (date != '' && todate == '') {
                url = `te/starsaathi/get-accept-liftings?page=${page}&fromDate=${moment(date).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            } else if (todate != '' && date == '') {
                url = `te/starsaathi/get-accept-liftings?page=${page}&toDate=${moment(todate).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            } else if (date !== '' && todate !== '') {
                url = `te/starsaathi/get-accept-liftings?page=${page}&fromDate=${moment(date).format('YYYY-MM-DD')}&toDate=${moment(todate).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            }
            getApiWithHeader(url)
                .then(response => {
                    if (!isFocusRef.current) {
                        //console.log('⛔ Skipping API because screen not focused')
                        return
                    }
                    if (response.data) {
                        setLoading(false)
                        setFlagEdit('approved')
                        var a = value
                        a = [...a, ...response.data.data.acceptedLists]

                        setListData(a)
                    }
                })
                .catch(err => {
                    setLoading(false)
                    
                })
        }
        if (type == 'rejected') {
            if (date != '' && todate == '') {
                url = `te/starsaathi/get-reject-liftings?page=${page}&fromDate=${moment(date).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            } else if (todate != '' && date == '') {
                url = `te/starsaathi/get-reject-liftings?page=${page}&toDate=${moment(todate).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            } else if (date !== '' && todate !== '') {
                url = `te/starsaathi/get-reject-liftings?page=${page}&fromDate=${moment(date).format('YYYY-MM-DD')}&toDate=${moment(todate).format('YYYY-MM-DD')}&preferred_app_lang=` + selectedLanguage()
            }
            getApiWithHeader(url)
                .then(response => {
                    if (!isFocusRef.current) {
                        //console.log('⛔ Skipping API because screen not focused')
                        return
                    }
                    if (response.data) {
                        setLoading(false)
                        setFlagEdit('rejected')
                        var a = value
                        a = [...a, ...response.data.data.rejectedLists]

                        setListData(a)
                    }
                })
                .catch(err => {
                    setLoading(false)
                    
                })
        }
    }

    const getDatewiseFilter = () => {
        if (flagEdit == 'pending') {
            getFilteredList('pending', fromDate, toDate, 1, [])
        } else if (flagEdit == 'approved') {
            getFilteredList('approved', fromDate, toDate, 1, [])
        } else if (flagEdit == 'rejected') {
            getFilteredList('rejected', fromDate, toDate, 1, [])
        }
    }

    const callSearchApi = (value, page, valueData) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        if (page == 1) {
            setListData([])
            setLoading(true)
        }
        let url = ''
        if (flagEdit == 'pending') {
            url = `te/starsaathi/get-pending-liftings?page=${page}&fromDate=${fromDate}&toDate=${toDate}&mason=${value}&preferred_app_lang=` + selectedLanguage()
        }
        if (flagEdit == 'approved') {
            url = `te/starsaathi/get-accept-liftings?page=${page}&fromDate=${fromDate}&toDate=${toDate}&mason=${value}&preferred_app_lang=` + selectedLanguage()
        }
        if (flagEdit == 'rejected') {
            url = `te/starsaathi/get-reject-liftings?page=${page}&fromDate=${fromDate}&toDate=${toDate}&mason=${value}&preferred_app_lang=` + selectedLanguage()
        }

        getApiWithHeader(url)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data) {
                    setLoading(false)
                    if (flagEdit == 'pending') {
                        var a = valueData
                        a = [...a, ...response.data.data.pendingLists]

                        setListData(response.data.data.pendingLists)
                    } else if (flagEdit == 'approved') {
                        var a = valueData
                        a = [...a, ...response.data.data.acceptedLists]

                        setListData(response.data.data.acceptedLists)
                    } else if (flagEdit == 'rejected') {
                        var a = valueData
                        a = [...a, ...response.data.data.rejectedLists]

                        setListData(response.data.data.rejectedLists)
                    }
                }
            })
            .catch(err => {
                setLoading(false)
                
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

    const selectPopupValue = (item) => {
        if (typePopup == 1) {
            setValue(item.value)
            setLabel(item.label)
            setPickerPopup(false)
            callSearchApi(item.value, 1, [])
        }
        if (typePopup == 2) { }
        if (typePopup == 3) { }

    }

    const renderInnerItem = ({ item, index }) => {
        return (
            <>
                {index < 8 ? <>
                    {item.key !== 'lifting_id' ? <View style={index % 2 == 1 ? { flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' } : { flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>
                                {index == 1 ? 'Product Name' : ''}
                                {index == 2 ? 'Qty Bags' : ''}
                                {index == 3 ? 'Date & Time' : ''}
                                {index == 4 ? 'RSSD Name' : ''}
                                {index == 5 ? 'Date of Lifting' : ''}
                                {index == 6 ? 'Remarks' : ''}
                                {index == 7 ? 'Status' : ''}
                            </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />

                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            {index == 7 ? <Text style={{ width: 70, color: '#52AF1D', fontSize: 15, textAlign: 'center', textAlignVertical: 'center', paddingVertical: 4, backgroundColor: '#E9FFDC', borderRadius: 5, borderWidth: 1, borderColor: '#52AF1D' }}>
                                Submit
                            </Text> : <Text style={{ color: 'black', fontSize: 15 }}>
                                {index == 1 ? convertForShowData(item.value) : ''}
                                {index == 2 ? convertForShowData(item.value) : ''}
                                {index == 3 ? convertForShowData(item.value) : ''}
                                {index == 4 ? convertForShowData(item.value) : ''}
                                {index == 5 ? '20-01-2025' : ''}
                                {index == 6 ? 'Remarks text' : ''}
                            </Text>}

                        </View>
                    </View> : null}
                </> : <></>}
            </>
        )
    }
    
    const renderList = ({ item }) => {
        return (
            <View style={{ width: '100%', flexDirection: 'column' }}>
                <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 10, paddingVertical: 10, borderRadius: 5 }}>
                    <FlatList
                        data={item.dataItem}
                        renderItem={renderInnerItem}
                        keyExtractor={(item) => item.key}
                    />

                    {/* {flagEdit == 'pending' ? <View style={{ height: 10 }}></View> : null}
                    {flagEdit == 'pending' ? (<View style={{ flexDirection: 'row', alignItems: 'center' }}>
                        <TouchableOpacity
                            onPress={() => callRejectLiftingApi(item)}
                            style={{ flex: 1, height: 38, justifyContent: 'center', alignItems: 'center', backgroundColor: '#EE1D23', borderRadius: 5, }}>
                            <Text style={{ color: '#FFFFFF', fontSize: 14, fontWeight: '600' }}>
                                {convertForShowData(textValue.REJECT)}
                            </Text>
                        </TouchableOpacity>
                        <View style={{ width: 5 }} />
                        <TouchableOpacity
                            onPress={() => goToEditLifting(item)}
                            style={{ flex: 1, height: 38, justifyContent: 'center', alignItems: 'center', backgroundColor: '#394D9F', borderRadius: 5, }}>
                            <Text style={{ color: '#FFFFFF', fontSize: 14, fontWeight: '600' }}>
                                {convertForShowData(textValue.EDIT)}
                            </Text>
                        </TouchableOpacity>
                        <View style={{ width: 5 }} />
                        <TouchableOpacity
                            onPress={() => {
                                setSlectedItem(item)
                                setModalVisible(true)
                            }}
                            style={{ flex: 1, height: 38, justifyContent: 'center', alignItems: 'center', backgroundColor: '#1F9A43', borderRadius: 5, }}>
                            <Text style={{ color: '#FFFFFF', fontSize: 14, fontWeight: '600' }}>
                                {convertForShowData(textValue.ACCEPT)}
                            </Text>
                        </TouchableOpacity>
                    </View>
                    ) : null} */}
                </View>
                <View style={{ height: 10 }} />
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
                        <Text style={{
                            fontSize: 20,
                            color: '#fff',
                            fontWeight: '600',
                            marginBottom: 20
                        }}>{convertForShowData(textValue.ORDER_ENQUIRY)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                        <View style={{ width: '100%', height: 55, flexDirection: 'row' }}>
                            <TouchableOpacity onPress={() => props.navigation.navigate('NewEnquiryRequest')} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#EE1D23', flexDirection: 'row', alignItems: 'center', justifyContent: 'center', paddingHorizontal: 10 }}>
                                <Text style={{ fontSize: 20, color: '#EE1D23' }}>{textValue.ADD_ENQUIRY}</Text>
                            </TouchableOpacity>

                        </View>
                        <View style={{ height: 20 }} />
                        <View style={{ width: '100%', height: 45, flexDirection: 'row' }}>
                            <TouchableOpacity onPress={() => setIsFromDate(true)} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFD5D6', flexDirection: 'row', alignItems: 'center', paddingHorizontal: 10 }}>
                                <Text style={{ color: '#000000', flex: 1 }}>{fromDate !== '' ? convertForShowData(fromDate) : convertForShowData(textValue.From_Date)}</Text>
                                <Image source={ImagePath.CalendarIcon} style={{ width: 20, height: 20, resizeMode: 'contain' }} />
                            </TouchableOpacity>
                            <View style={{ width: 10 }} />
                            <TouchableOpacity onPress={() => setIsToDate(true)} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFD5D6', flexDirection: 'row', alignItems: 'center', paddingHorizontal: 10 }}>
                                <Text style={{ color: '#000000', flex: 1 }}>{toDate !== '' ? convertForShowData(toDate) : convertForShowData(textValue.To_Date)}</Text>
                                <Image source={ImagePath.CalendarIcon} style={{ width: 20, height: 20, resizeMode: 'contain' }} />
                            </TouchableOpacity>
                            <View style={{ width: 10 }} />
                            <TouchableOpacity
                                activeOpacity={0.8}
                                onPress={() => getDatewiseFilter()}
                                style={{ width: 45, height: 45, borderRadius: 10, backgroundColor: '#FFF2F2', alignItems: 'center', justifyContent: 'center' }}>
                                <Image source={ImagePath.SearchIcon} style={{ height: 20, width: 20, resizeMode: 'contain', tintColor: '#EE1D23' }} />
                            </TouchableOpacity>
                        </View>
                        <View style={{ height: 10 }} />
                        <FlashList
                            data={listData}
                            renderItem={renderList}
                            showsHorizontalScrollIndicator={false}
                            showsVerticalScrollIndicator={false}
                        />
                    </View>
                </View>
            </View>

            <DateTimePickerModal
                isVisible={isFromDate}
                mode='date'
                minimumDate={new Date(1900, 0, 1)}
                onConfirm={handleConfirmFrom}
                onCancel={hideDatePickerFrom}
                date={new Date()}
            />

            <DateTimePickerModal
                isVisible={isToDate}
                mode='date'
                minimumDate={new Date(1900, 0, 1)}
                onConfirm={handleConfirmTo}
                onCancel={hideDatePickerTo}
                date={new Date()}
            />
            <Modal
                animationType='slide'
                transparent={true}
                visible={modalVisible}
            >
                <View style={styles.centeredView}>
                    <View style={styles.modalView}>
                        <Text style={styles.modalText}>{convertForShowData(textValue.Are_you_want_to_accept_the_lifting)}</Text>
                        <View style={{ flexDirection: 'row', justifyContent: 'space-between', paddingTop: 26 }}>
                            <TouchableOpacity onPress={() => setModalVisible(false)} style={{ height: 50, width: 131, justifyContent: 'center', backgroundColor: '#EE1D23', borderRadius: 25, margin: 4, justifyContent: 'center', alignItems: 'center' }}>
                                <Text style={{ color: '#FFFFFF', fontSize: 14 }}>{convertForShowData(textValue.REJECT)}</Text>
                            </TouchableOpacity>
                            <TouchableOpacity onPress={() => callAcceptApi(selectedItem)} style={{ height: 50, width: 131, justifyContent: 'center', backgroundColor: '#509F39', borderRadius: 25, margin: 4, justifyContent: 'center', alignItems: 'center' }}>
                                <Text style={{ color: '#FFFFFF', fontSize: 14 }}>{convertForShowData(textValue.ACCEPT)}</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </Modal>
            {loading ? <Loader /> : null}
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
export default OrderEnquiryList
