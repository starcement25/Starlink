import React, { useCallback, useEffect, useRef, useState } from 'react'
import { SafeAreaView, Text, View, Image, TouchableOpacity, Platform, FlatList, ActivityIndicator, ScrollView, Dimensions, Keyboard, TextInput } from 'react-native'
import styles from './RewardDetailsStyle'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import moment from 'moment'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import ImagePath from '../../../image/ImagePath'
import LinearGradient from 'react-native-linear-gradient'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import DataStore from '../../../helper/constants/DataStore'
import { FlashList } from '@shopify/flash-list'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

var user_details
var total_point
var listingData = []

const RewardDetails = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [isKeyboardVisible, setKeyboardVisible] = useState(false)
    const [keyboardHeight, setKeyboardHeight] = useState(0)

    const [loading, setLoading] = useState(false)
    const [searchTxt, setSearchTxt] = useState('')
    const [rewardHistory, setRewardHistory] = useState([])
    const [copyrewardHistory, setCopyrewardHistory] = useState([])

    const [openMnt, setOpenMnt] = useState(false)
    const [months, setMonths] = useState([])

    const [openYr, setOpenYr] = useState(false)
    const [yrs, setYrs] = useState(
        [
            { label: convertForShowData('2022'), value: '2022' },
            { label: convertForShowData('2023'), value: '2023' },
            { label: convertForShowData('2024'), value: '2024' },
            { label: convertForShowData('2025'), value: '2025' },
            { label: convertForShowData('2026'), value: '2026' },
            { label: convertForShowData('2027'), value: '2027' },
            { label: convertForShowData('2028'), value: '2028' },
            { label: convertForShowData('2029'), value: '2029' },
            { label: convertForShowData('2030'), value: '2030' }
        ])

    const [openMason, setOpenMason] = useState(false)
    const [masonList, setMasonList] = useState([])
    const [visibleRedeem, setVisibleReedem] = useState('')

    const [flatlistLoader, setFlatListLoader] = useState(true)

    const [pickerPopup, setPickerPopup] = useState(false)
    const [dataSetList, setDataSetList] = useState([])
    const [typePopup, setTypePopup] = useState(0)
    const [searchText, setSearchText] = useState('')
    const [valueMnt, setValueMnt] = useState('')
    const [labelMnt, setLabelMnt] = useState(textValue.Month)
    const [valueYr, setValueYr] = useState('')
    const [labelYr, setLabelYr] = useState(textValue.Year)
    const [valueMason, setValueMason] = useState(0)
    const [labelMason, setLabelMason] = useState(textValue.Select_Mason)

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            my_profile()
            getSettings()
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
    }, [])

    useEffect(() => {
        const showSubscription = Keyboard.addListener('keyboardDidShow', (event) => {
            setKeyboardHeight(event.endCoordinates.height)
            setKeyboardVisible(true)
        })

        const hideSubscription = Keyboard.addListener('keyboardDidHide', () => {
            setKeyboardHeight(0)
            setKeyboardVisible(false)
        })

        // Cleanup the event listeners when the component unmounts
        return () => {
            showSubscription.remove()
            hideSubscription.remove()
        }
    }, [])
    useEffect(() => {
        if (typePopup == 2) {
            if (searchText == '') {
                setDataSetList(masonList)
            } else {
                const results = masonList.filter((item) =>
                    item.label.toLowerCase().includes(searchText.toLowerCase())
                )
                setDataSetList(results)
            }
        }
    }, [masonList])

    useEffect(() => {
        let isCancelled = false // Cancellation flag

        const startFetchingRewards = async () => {
            setSearchText('')
            setPickerPopup(false)
            setRewardHistory([])
            setCopyrewardHistory([])
            total_point = 0



            const fetchRewards = async (page_value, id, value) => {
                if (isCancelled) return // Stop the function if cancelled

                setFlatListLoader(true)

                var FormData = require('form-data')
                var formData = new FormData()
                formData.append('user_id', id)
                formData.append('preferred_app_lang', selectedLanguage())

                try {
                    const response = await postApiWithHeader(
                        `${constants.get_rewards_by_mason}?page=${page_value}&preferred_app_lang=${selectedLanguage()}`,
                        formData
                    )

                    setLoading(false)

                    if (response.data.status && !isCancelled) {
                        const newData = response.data.data
                        const updatedValue = [...value, ...newData]

                        setRewardHistory(updatedValue)
                        setCopyrewardHistory(updatedValue)


                        // Recursively fetch next page if there is more data
                        if (newData.length > 0) {
                            fetchRewards(page_value + 1, id, updatedValue)
                        } else {
                            setFlatListLoader(false) // No more data to fetch
                        }

                        if (page_value === 1) {
                            total_point = response.data.net_point // Save total points
                        }
                    } else {
                        if (page_value === 1) {
                            showToast('error', response.data.msg)
                        }
                        setFlatListLoader(false)
                    }
                } catch (err) {
                    
                    setLoading(false)
                    setFlatListLoader(false)
                }
            }

            // Start fetching rewards with page 1
            fetchRewards(1, valueMason, [])
        }

        // Start fetching
        startFetchingRewards()

        // Cleanup function to cancel any ongoing fetch
        return () => {
            isCancelled = true
        }
    }, [valueMason])

    const searchDealer = (text) => {
        const results = masonList.filter((item) =>
            item.label.toLowerCase().includes(text.toLowerCase())
        )
        setSearchText(text)
        setDataSetList(results)
    }

    const getSettings = async () => {
        getApiWithHeader(constants.app_registration_link_visible + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data.status) {

                    setVisibleReedem(response?.data?.data[0]?.app_redeem_now_button)
                }
            })
            .catch(err => {

            })
    }

    const get_mason_list = async (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        getApiWithHeader(constants.get_my_mason + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {

                    let data = response.data.data
                    let masonList = []
                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            // label: convertForShowData(data[i].mason_name) + ' ' + convertForShowData(data[i].mason_phone),
                            label: convertForShowData(data[i].mason_name),
                            value: data[i].mason_id
                        }
                        masonList.push(obj)
                    }
                    var a = value
                    a = [...a, ...masonList]
                    setMasonList(masonList)
                    setLoading(false)
                    get_mason_list(page_value + 1, a)
                }
                else {
                    setLoading(false)
                    if (page_value == 1) { showToast('error', response.data.msg) }
                }
            })
            .catch(err => {

                setLoading(false)
                // showToast('error', messageList.t4)
            })
    }

    const my_profile = () => {
        listingData = []
        getApiWithHeader(constants.my_profile + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    user_details = response?.data
                    total_point = response?.data?.data?.points
                    if (response?.data?.data?.role == 2) {
                        total_point = 0
                        get_rewards(1, response?.data?.data?.id, [])
                    } else {
                        get_mason_list(1, [])
                    }
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
                setFlatListLoader(false)
            })
            .catch(err => {

                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const get_rewards = async (page_value, id, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }

        setFlatListLoader(true)

        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('user_id', id)
        formData.append('preferred_app_lang', selectedLanguage())
        postApiWithHeader(constants.get_rewards_by_mason + `?page=${page_value}&preferred_app_lang=` + selectedLanguage(), formData)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                setLoading(false)
                if (response.data.status) {
                    listingData = response.data.data

                    var a = value
                    a = [...a, ...response.data.data]
                    setRewardHistory(a)
                    setCopyrewardHistory(a)
                    get_rewards(page_value + 1, id, a)
                    if (page_value == 1) {
                        total_point = response.data.net_point
                    }
                } else {
                    if (page_value == 1) {
                        showToast('error', response.data.msg)
                    }
                    setFlatListLoader(false)
                }
            })
            .catch(err => {
                
                setLoading(false)
                // showToast('error', messageList.t4)
                setFlatListLoader(false)
            })
    }

    const showToast = (type, msg) => {
        Toast.show({
            type: type,
            text2: msg,
            text2NumberOfLines: 2
        })
    }

    const filder_by_dropdown = async () => {
        let array_list = []
        setSearchTxt('')
        if (valueMnt && valueYr) {
            for (var i = 0; i < copyrewardHistory.length; i++) {
                let month = moment(copyrewardHistory[i].reward_date, 'YYYY-MM-DD h:mm:ss').format('M')
                let year = moment(copyrewardHistory[i].reward_date, 'YYYY-MM-DD h:mm:ss').format('YYYY')
                if (month == valueMnt && year == valueYr) {
                    array_list.push(copyrewardHistory[i])
                }
            }
            setRewardHistory(array_list)
        } else if (valueYr) {
            for (var i = 0; i < copyrewardHistory.length; i++) {
                let year = moment(copyrewardHistory[i].reward_date, 'YYYY-MM-DD h:mm:ss').format('YYYY')
                if (year == valueYr) {
                    array_list.push(copyrewardHistory[i])
                }
            }
            setRewardHistory(array_list)
        } else {
            for (var i = 0; i < copyrewardHistory.length; i++) {
                let month = moment(copyrewardHistory[i].reward_date, 'YYYY-MM-DD h:mm:ss').format('M')
                if (month == valueMnt) {
                    array_list.push(copyrewardHistory[i])
                }
            }
            setRewardHistory(array_list)
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
            setSearchText('')
            setPickerPopup(false)
            filder_by_dropdown()
        }
        if (typePopup == 1) {
            setLabelYr(item.label)
            setValueYr(item.value)
            setSearchText('')
            setPickerPopup(false)
            filder_by_dropdown()
        }
        if (typePopup == 2) {
            setLabelMason(item.label)
            setValueMason(() => 0)
            setValueMason(() => item.value)
        }
    }
    const renderItem = ({ item }) => (
        <View style={{ width: '100%', justifyContent: 'center', alignItems: 'center' }}>
            {user_details?.data?.role == 2 ? <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 10, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DATE)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(moment(item?.reward_date, 'YYYY-MM-DD h:mm:ss').format('DD-MM-YYYY'))}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DESCRIPTION)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.description)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)} {convertForShowData(textValue.EARNED)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.credit_point)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)} {convertForShowData(textValue.REDEEMED)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.debit_point)}</Text>
                    </View>
                </View>
            </View> : <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 10, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DATE)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(moment(item?.reward_date, 'YYYY-MM-DD h:mm:ss').format('DD-MM-YYYY'))}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DESCRIPTION)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.description)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)} {convertForShowData(textValue.EARNED)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.credit_point)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)} {convertForShowData(textValue.REDEEMED)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.debit_point)}</Text>
                    </View>
                </View>
            </View>}
        </View>
    )

    const renderFooter = () => {
        if (!flatlistLoader) return null
        return (
            <View style={{
                paddingHorizontal: 20,
                paddingTop: 20,
                paddingBottom: 90,
                alignItems: 'center',
            }}>
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
                        <Text style={styles._upperView._txt}>{convertForShowData(textValue.REWARD_DETAILS)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() =>{
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
                        {user_details?.data?.role == 2 ? <LinearGradient colors={['#EE1D23', '#AF1317']} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={{ width: '100%', height: 75, borderRadius: 10 }}>
                            <View style={{ width: '100%', height: '100%' }}>
                                <View style={{ width: '100%', height: '100%', flexDirection: 'row-reverse' }}>
                                    <Image source={ImagePath.DesingBackgroundStar} style={{ width: 55, height: 55, resizeMode: 'contain' }} />
                                </View>
                                <View style={{ width: '100%', height: '100%', position: 'absolute', paddingHorizontal: 20, alignItems: 'center', justifyContent: 'center' }}>
                                    <View style={{ flexDirection: 'column', width: '100%' }}>
                                        <Text style={{ fontWeight: '600', fontSize: 12, color: '#FFF' }}>{convertForShowData(textValue.AVAILABLE_POINTS.toString())}</Text>
                                        <Text style={{ fontWeight: 'bold', fontSize: 32, color: '#FFF' }}>{total_point == null || total_point == 'null' ? convertForShowData('0') : Number(convertForShowData(total_point.toString())).toFixed(2)}</Text>
                                    </View>
                                </View>
                            </View>
                        </LinearGradient> : null}
                        {user_details?.data?.role != 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                            <View style={{ width: '100%', flexDirection: 'row', elevation: 5 }}>
                                <TouchableOpacity
                                    activeOpacity={0.8}
                                    onPress={() => {
                                        setTypePopup(0)
                                        setDataSetList(months)
                                        setSearchText('')
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
                                        setSearchText('')
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
                                        setValueYr(0)
                                        setValueMnt(0)
                                        setValueMason(0)
                                        setRewardHistory([])
                                        setCopyrewardHistory([])
                                        setLoading(true)
                                        my_profile()
                                    }}
                                    style={{ width: 45, height: 45, borderRadius: 10, backgroundColor: '#FFF2F2', alignItems: 'center', justifyContent: 'center' }}>
                                    <Image source={ImagePath.ReloadIcon} style={{ height: 20, width: 20, resizeMode: 'contain' }} />
                                </TouchableOpacity>
                            </View>
                            <View style={{ height: 10 }} />
                        </View> : null}

                        {user_details?.data?.role != 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                            <View style={{ width: '100%', flexDirection: 'row', elevation: 5 }}>
                                <TouchableOpacity
                                    activeOpacity={0.8}
                                    onPress={() => {
                                        setTypePopup(2)
                                        setDataSetList(masonList)
                                        setSearchText('')
                                        setPickerPopup(true)
                                    }}
                                    style={{ flex: 1, height: 45, borderRadius: 10, borderColor: '#FFD5D6', borderWidth: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 10, flexDirection: 'row' }}>
                                    <Text style={{ flex: 1, color: '#000' }}>{convertForShowData(labelMason)}</Text>
                                    <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 15, resizeMode: 'contain' }} />
                                </TouchableOpacity>
                            </View>
                            <View style={{ height: 10 }} />
                        </View> : null}
                        {user_details?.data?.role != 2 ? <LinearGradient colors={['#EE1D23', '#AF1317']} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={{ width: '100%', height: 75, borderRadius: 10 }}>
                            <View style={{ width: '100%', height: '100%' }}>
                                <View style={{ width: '100%', height: '100%', flexDirection: 'row-reverse' }}>
                                    <Image source={ImagePath.DesingBackgroundStar} style={{ width: 55, height: 55, resizeMode: 'contain' }} />
                                </View>
                                <View style={{ width: '100%', height: '100%', position: 'absolute', paddingHorizontal: 20, alignItems: 'center', justifyContent: 'center' }}>
                                    <View style={{ flexDirection: 'column', width: '100%' }}>
                                        <Text style={{ fontWeight: '600', fontSize: 12, color: '#FFF' }}>{convertForShowData(textValue.AVAILABLE_POINTS.toString())}</Text>
                                        <Text style={{ fontWeight: 'bold', fontSize: 32, color: '#FFF' }}>{total_point == null || total_point == 'null' ? convertForShowData('0') : convertForShowData(Number(total_point.toString()).toFixed(2))}</Text>
                                    </View>
                                </View>
                            </View>
                        </LinearGradient> : null}
                        <View style={{ height: 10 }} />
                        <FlashList
                            data={rewardHistory}
                            renderItem={renderItem}
                            ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                        />
                    </View>
                </View>
            </View>

            {pickerPopup ? <View style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0006' }}>
                <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                    <TouchableOpacity onPress={() => setPickerPopup(false)} style={{ minHeight: 120, flex: 1 }} />
                    <View style={{ width: '100%', paddingHorizontal: 20, paddingTop: 20, flexDirection: 'column', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                        <View style={{ width: '100%', minHeight: isKeyboardVisible ? 200 + keyboardHeight : 200, backgroundColor: '#FFE8E9', padding: 15, borderTopLeftRadius: 10, borderTopRightRadius: 10 }}>
                            <View style={{ width: '100%', height: 45, backgroundColor: '#FFF', borderRadius: 10, paddingHorizontal: 20, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                <TextInput style={{ flex: 1, height: '100%', color: '#000' }} placeholder={textValue.Search} placeholderTextColor={'#bbb'} onChangeText={searchDealer} />
                                <View style={{ width: 1, height: '70%', backgroundColor: '#FFE8E9' }} />
                                <View style={{ width: 15 }} />
                                <Image style={{ width: 20, height: 20, tintColor: '#000' }} source={Icons.search} />
                            </View>
                            <View style={{ height: 10 }} />
                            <FlatList
                                style={{ maxHeight: Dimensions.get('screen').height - 400, minHeight: 200 }}
                                data={dataSetList}
                                renderItem={renderPopupItem}
                                keyExtractor={item => item.id}
                            />
                        </View>
                    </View>
                </View>
            </View> : null}
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default RewardDetails