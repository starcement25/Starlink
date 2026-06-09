import React, { useState, useEffect, useRef, useCallback } from 'react'
import { SafeAreaView, ScrollView, Text, View, Image, TouchableOpacity, Platform, TextInput, FlatList, Keyboard } from 'react-native'
import styles from './DealerRequestStyle'
import DropDownPicker from 'react-native-dropdown-picker'
import Toast from 'react-native-toast-message'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

const DealerRequest = props => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [isKeyboardVisible, setKeyboardVisible] = useState(false)
    const [keyboardHeight, setKeyboardHeight] = useState(0)

    const [loading, setLoading] = useState(false)
    const [dealerRssdList, setDealerRssdList] = useState([])
    const [starSathibtn, setStarsathibtn] = useState(false)
    const [min, setMin] = useState('')
    const [max, setMax] = useState('')
    const [dealerIds, setDelaerids] = useState([])
    const [dropPlace, setDropPlace] = useState('')

    const [pickerPopup, setPickerPopup] = useState(false)
    const [dataSetList, setDataSetList] = useState([])
    const [typePopup, setTypePopup] = useState(0)
    const [flatlistLoader, setFlatlistLoader] = useState(false)

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setLoading(true)
            getSettings()
            get_all_dealers(1, [])
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

        // Cleanup the event listeners when the component unmounts
        return () => {
            showSubscription.remove()
            hideSubscription.remove()
        }
    }, [])

    useEffect(() => {
        if (typePopup == 1) {
            setDataSetList(dealerRssdList)
        }
    }, [dealerRssdList])

    const getSettings = async () => {
        getApiWithHeader(constants.app_registration_link_visible + '?preferred_app_lang' + selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    if (response?.data?.data[0]?.lifting_send_star_sathi_app_button == '1') {
                        setStarsathibtn(true)
                    }
                    setMax(response?.data?.data[0]?.maximum_lifting_limit)
                    setMin(response?.data?.data[0]?.minimum_lifting_limit)
                }
            })
            .catch(err => {
                
            })
    }

    const get_all_dealers = async (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        getApiWithHeader(constants.linking_dealer + `?page=${page_value}&preferred_app_lang` + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                setLoading(false)
                if (response.data.status) {
                    response.data.data = response.data.data.map((item) => {
                        // Example modification: Adding a prefix to the `label`
                        item.label = convertForShowData(item.label)
                        return item
                    })
                    var a = value
                    for (var i = 0; i < response.data.data.length; i++) {
                        var obj = response.data.data[i]
                        var isSelected = false
                        obj = { ...obj, isSelected }
                        a.push(obj)
                    }
                    // a = [...a, ...response.data.data]
                    setDealerRssdList(a)
                    setDropPlace(response.data.drop_down_placeholder)
                    get_all_dealers(1 + page_value, a)
                } else {
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else {
                        if (page_value == 1) {
                            showToast('error', response.data.msg)
                        }
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

    const callLinkApi = () => {
        setLoading(true)
        let obj = {
            dealer_ids: dealerIds,
            preferred_app_lang: selectedLanguage()
        }
        postApiWithHeader(constants.link_dealer, obj)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    setDelaerids([])
                    props.navigation.navigate('Dashboard')
                } else {
                    showToast('error', response.data.msg)
                }
            })
            .catch(err => {

                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const setDataAndClosePopup = () => {
        const selectedDealers = dealerRssdList.filter(item => item.isSelected).map(item => item.value)
        setDelaerids(selectedDealers)
        const selectedDealersName = dealerRssdList.filter(item => item.isSelected).map(item => item.label).join(', ')

        setDropPlace(selectedDealersName)
        setPickerPopup(false)
    }

    const changeStatus = (data) => {
        setDealerRssdList((dealerRssdList) =>
            dealerRssdList.map(item => {
                if (data?.value !== undefined) {
                    return {
                        ...item,
                        isSelected: item.value === data.value ? !item.isSelected : item.isSelected,
                    }
                }
                return item
            })
        )
        setDataSetList((dataSetList) =>
            dataSetList.map(item => {
                if (data?.value !== undefined) {
                    return {
                        ...item,
                        isSelected: item.value === data.value ? !item.isSelected : item.isSelected,
                    }
                }
                return item
            })
        )
    }

    const searchDealer = (text) => {
        const results = dealerRssdList.filter((item) =>
            item.keyword.toLowerCase().includes(text.toLowerCase())
        )

        setDataSetList(results)
    }

    const renderItem = ({ item }) => {
        return (
            <TouchableOpacity onPress={() => { changeStatus(item) }}>
                <View style={{ width: '100%', height: 40, paddingHorizontal: 10, alignItems: 'center', justifyContent: 'center', flexDirection: 'row', backgroundColor: !item?.isSelected ? '#fff' : '#EE1D23', marginVertical: 5, borderRadius: 5 }}>
                    <Text style={{ flex: 1, color: !item?.isSelected ? '#000' : '#FFF', fontSize: 14 }}>{convertForShowData(item?.label)}</Text>
                </View>
            </TouchableOpacity>
        )
    }

    const renderFooter = () => {
        if (!flatlistLoader) return <View style={{ height: 300 }} />
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
            <View style={{ width: '100%', height: '100%', flexDirection: 'column', backgroundColor: '#FFF' }}>
                <View style={{ width: '100%', height: 100, borderBottomLeftRadius: 25, borderBottomRightRadius: 25, backgroundColor: '#EE1D23' }} />
            </View>
            <View style={{ width: '100%', height: '100%', position: 'absolute', flexDirection: 'column' }}>
                <View style={{ height: Platform.OS == 'ios' ? 25 : 0 }} />
                <View style={{ width: '100%', height: 70 }}>
                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
                        <Text style={styles._upperView._txt}>{convertForShowData(textValue.REQUEST_A_DEALER)}</Text>
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
                        <TouchableOpacity onPress={() => {
                            setTypePopup(1)
                            setDataSetList(dealerRssdList)
                            setPickerPopup(true)
                        }} style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                            <Text style={{ flex: 1, color: '#000' }}>{dropPlace == '' ? textValue.Select_Dealer : convertForShowData(dropPlace)}</Text>
                            <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                            <View style={{ width: 10 }} />
                        </TouchableOpacity>

                        <View>
                            <TouchableOpacity onPress={() => callLinkApi()} style={{ width: '100%', height: 56, backgroundColor: 'red', borderRadius: 30, marginTop: 20, justifyContent: 'center', alignItems: 'center' }}>
                                <Text style={{ color: '#FFFFFF', fontSize: 16 }}>{convertForShowData(textValue.LINK)}</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </View>
            {loading ? <Loader /> : null}
            {pickerPopup ? <View style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0006' }}>
                <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                    <TouchableOpacity onPress={() => setDataAndClosePopup()} style={{ minHeight: 120, flex: 1 }} />
                    <View style={{ width: '100%', minHeight: isKeyboardVisible ? 200 + keyboardHeight : 200, paddingHorizontal: 20, paddingTop: 20, flexDirection: 'column', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                        <View style={{ width: '100%', flexDirection: 'row-reverse' }}>
                            <TouchableOpacity onPress={() => { setDataAndClosePopup() }}>
                                <Text style={{ fontSize: 16, color: '#000' }}>X</Text>
                            </TouchableOpacity>
                        </View>
                        {flatlistLoader ? <>
                            <View style={{ width: '100%', flexDirection: 'row-reverse' }}>
                                <ActivityIndicator size={20} color='#ee1d23' />
                                <View style={{ width: 10 }} />
                                <Text style={{ color: '#ee1d23' }}>{convertForShowData(textValue.Loading)}...</Text>
                            </View>
                            <View style={{ height: 5 }} />
                        </> : null}
                        <View style={{ height: 10 }} />
                        <View style={{ width: '100%', backgroundColor: '#FFE8E9', padding: 15, borderTopLeftRadius: 10, borderTopRightRadius: 10 }}>
                            <View style={{ width: '100%', height: 45, backgroundColor: '#FFF', borderRadius: 10, paddingHorizontal: 20, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                <TextInput style={{ flex: 1, height: '100%', color: '#000' }} placeholder={textValue.Search} placeholderTextColor={'#bbb'} editable={!flatlistLoader} onChangeText={searchDealer} />
                                <View style={{ width: 1, height: '70%', backgroundColor: '#FFE8E9' }} />
                                <View style={{ width: 15 }} />
                                <Image style={{ width: 20, height: 20, tintColor: '#000' }} source={Icons.search} />
                            </View>
                            <View style={{ height: 10 }} />
                            <FlatList
                                data={dataSetList}
                                renderItem={renderItem}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                ListFooterComponent={renderFooter}
                                keyExtractor={item => item.id}
                            />
                        </View>
                    </View>
                </View>
            </View> : null}
        </SafeView>
    )
}

export default DealerRequest