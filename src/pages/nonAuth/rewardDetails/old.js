import React, { useEffect, useState } from 'react'
import { SafeAreaView, Text, View, Image, TouchableOpacity, Platform, FlatList, ActivityIndicator } from 'react-native'
import styles from './RewardDetailsStyle'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import Icon from 'react-native-vector-icons/FontAwesome'
import DropDownPicker from 'react-native-dropdown-picker'
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
var total_point
var listingData = []

const RewardDetails = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [searchTxt, setSearchTxt] = useState('')
    const [rewardHistory, setRewardHistory] = useState([])
    const [copyrewardHistory, setCopyrewardHistory] = useState([])

    const [openMnt, setOpenMnt] = useState(false)
    const [valueMnt, setValueMnt] = useState('')
    const [months, setMonths] = useState([])

    const [openYr, setOpenYr] = useState(false)
    const [valueYr, setValueYr] = useState('')
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
    const [valueMason, setValueMason] = useState('')
    const [masonList, setMasonList] = useState([])
    const [visibleRedeem, setVisibleReedem] = useState('')

    const [flatlistLoader,setFlatListLoader]=useState(true)

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
        const focusListener = props.navigation.addListener('focus', () => {
            my_profile()
            getSettings()
        })
        return focusListener
    }, [props.navigation])

    const getSettings = async () => {
        getApiWithHeader(constants.app_registration_link_visible+'?preferred_app_lang='+selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    setVisibleReedem(response?.data?.data[0]?.app_redeem_now_button)
                }
            })
            .catch(err => {
                
            })
    }

    const get_mason_list = async (page_value,value) => {
        getApiWithHeader(constants.get_my_mason+'?page='+page_value+'&preferred_app_lang='+selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    let data = response.data.data
                    let masonList = []
                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            label: convertForShowData(data[i].mason_name) + ' ' + convertForShowData(data[i].mason_phone),
                            value: data[i].mason_id
                        }
                        masonList.push(obj)
                    }
                    var a = value
                    a = [...a, ...masonList]
                    setMasonList(masonList)
                    setLoading(false)
                    get_mason_list(page_value+1,a)
                }
                else {
                    setLoading(false)
                    if(page_value==1){showToast('error', response.data.msg)}
                }
            })
            .catch(err => {
                
                setLoading(false)
                // showToast('error', messageList.t4)
            })
    }

    const my_profile = () => {
        listingData = []
        getApiWithHeader(constants.my_profile+'?preferred_app_lang='+selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    user_details = response?.data
                    total_point = response?.data?.data?.points
                    if (response?.data?.data?.role == 2) {
                        get_rewards(1,response?.data?.data?.id,[])
                    } else {
                        get_mason_list(1,[])
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

    const get_rewards = async (page_value,id,value) => {
        setFlatListLoader(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('user_id', id)
        formData.append('preferred_app_lang', selectedLanguage())
        postApiWithHeader(constants.get_rewards_by_mason+`?page=${page_value}&preferred_app_lang=`+selectedLanguage(), formData)
            .then(response => {
                setLoading(false)
                
                if (response.data.status) {
                    listingData = response.data.data
                    // total_point = response.data.net_point
                    var a = value
                    a = [...a, ...response.data.data]
                    setRewardHistory(a)
                    setCopyrewardHistory(a)
                    get_rewards(page_value+1,id,a)
                } else {
                    if(page_value==1){
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
            text2NumberOfLines:2
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
            for (var i = 0 ;i < copyrewardHistory.length; i++) {
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
        } catch (e) {}
    }

    const renderItem = ({ item }) => (
        <View style={{ width: '100%', justifyContent: 'center', alignItems: 'center' }}>
            {user_details?.data?.role == 2 ?
                <View style={{ width: '90%', margin: 10, position: 'relative' }}>
                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DATE)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(moment(item?.reward_date, 'YYYY-MM-DD h:mm:ss').format('DD-MM-YYYY'))}</Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DESCRIPTION)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.description)}</Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)}</Text>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.EARNED)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.credit_point)}</Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5, marginBottom: 8 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)}</Text>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.REDEEMED)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.debit_point)}</Text>
                        </View>
                    </View>

                    <View style={{ width: '100%', height: 1, backgroundColor: 'gray', position: 'absolute', bottom: 0 }}></View>
                </View> : <View style={{ width: '90%', margin: 10, position: 'relative' }}>
                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DATE)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(moment(item?.reward_date, 'YYYY-MM-DD h:mm:ss').format('DD-MM-YYYY'))}</Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DESCRIPTION)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.description)}</Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)}</Text>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.EARNED)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.credit_point)}</Text>
                        </View>
                    </View>

                    <View style={{ flexDirection: 'row', flex: 3, marginTop: 5, marginBottom: 8 }}>
                        <View style={{ flex: 1 }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.POINTS)}</Text>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.REDEEMED)}</Text>
                        </View>
                        <View style={{ flex: 2 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item?.debit_point)}</Text>
                        </View>
                    </View>

                    <View style={{ width: '100%', height: 1, backgroundColor: 'gray', position: 'absolute', bottom: 0 }}></View>
                </View>}
        </View>
    )

    const renderFooter = () => {
        if (!flatlistLoader) return null
        return (
          <View style={{
            paddingHorizontal: 20,
            paddingTop:20,
            paddingBottom:90,
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
                    <TouchableOpacity style={styles._upperView._back_btn}
                        onPress={() =>{
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}>
                        <Image style={styles._upperView._back_btn._img} source={Icons.back} />
                    </TouchableOpacity>
                    <View style={{ justifyContent: 'center', alignItems: 'center', height: '100%' }}>
                        <Text style={[styles._upperView._txt, { marginBottom: user_details?.data?.role == 2 ? 0 : 20 }]}>{convertForShowData(textValue.REWARD_DETAILS)}</Text>
                        {user_details?.data?.role == 2 ? null :<></>}
                    </View>
                </View>

                <View style={styles._lowerView}>
                    {user_details?.data?.role == 2 ? <View style={{ flexDirection: 'row', justifyContent: 'center', marginTop: 20, marginBottom: -10 }}>
                        <Text style={{ textAlign: 'center', marginBottom: 10, fontSize: 16, fontWeight: '700' }}>{total_point == null || total_point == 'null' ? convertForShowData(textValue.NET_POINTS) + ' : ' + convertForShowData('0') : convertForShowData(textValue.NET_POINTS) + ' : ' + convertForShowData(total_point)}</Text>
                        <TouchableOpacity
                            style={{ width: 40, justifyContent: 'center', alignItems: 'center' }}
                            activeOpacity={0.8}
                            onPress={() => {
                                setRewardHistory([])
                                setCopyrewardHistory([])
                                setLoading(true)
                                setValueMnt('')
                                setValueYr('')
                                my_profile()
                            }}>
                            <Icon name='refresh' size={20} color='#900' />
                        </TouchableOpacity>
                    </View> : null}
                    {user_details?.data?.role != 2 ? <View style={styles._center_dropdown_view}>
                        <View style={[styles._dropdown_view, { height: Platform.OS == 'android' ? openMnt || openYr ? 175 : 35 : 35 }]}>
                            <View style={[styles._dropdown_view._left_view, { height: Platform.OS == 'android' ? openMnt ? 175 : 35 : 35 }]}>
                                <DropDownPicker
                                    listMode='SCROLLVIEW'
                                    scrollViewProps={{nestedScrollEnabled: true,}}
                                    style={{
                                        backgroundColor: '#fff00000',
                                        borderColor: '#a8a8a800',
                                        borderRadius: 25,
                                        width: '90%',
                                        marginTop: -10,
                                        marginLeft: 10
                                    }}
                                    open={openMnt}
                                    value={valueMnt}
                                    items={months}
                                    setOpen={setOpenMnt}
                                    setValue={setValueMnt}
                                    placeholder={textValue.Month}
                                    onChangeValue={(value) => {
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
                                        height: 150
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
                            <View style={[styles._dropdown_view._right_view, { height: Platform.OS == 'android' ? openYr ? 175 : 35 : 35 }]}>
                                <DropDownPicker
                                    listMode='SCROLLVIEW'
                                    scrollViewProps={{nestedScrollEnabled: true,}}
                                    style={{
                                        backgroundColor: '#fff00000',
                                        borderColor: '#a8a8a800',
                                        borderRadius: 25,
                                        width: '90%',
                                        marginTop: -10,
                                        marginLeft: 10
                                    }}
                                    open={openYr}
                                    value={valueYr}
                                    items={yrs}
                                    setOpen={setOpenYr}
                                    setValue={setValueYr}
                                    placeholder={textValue.Year}
                                    onChangeValue={(value) => {
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
                                        height: 150
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
                                    setValueYr(0)
                                    setValueMnt(0)
                                    setValueMason(0)
                                    setRewardHistory([])
                                    setCopyrewardHistory([])
                                    setLoading(true)
                                    my_profile()
                                }}
                            >
                                <Icon name='refresh' size={20} color='#900' />
                            </TouchableOpacity>

                        </View>
                    </View> : null}

                    {user_details?.data?.role != 2 ? <View style={{ justifyContent: 'center', alignItems: 'center', zIndex: 99, marginTop: Platform.OS == 'android' ? openYr || openMnt ? -140 : 0 : null }}>
                        <View style={{ width: '90%', marginTop: Platform.OS == 'android' ? openYr || openMnt ? 20 : 20 : 20, borderRightColor: openMason ? '#00000000' : '#D5D5D5', borderLeftColor: openMason ? '#00000000' : '#D5D5D5', borderTopColor: openMason ? '#00000000' : '#D5D5D5', borderBottomColor: openMason ? '#00000000' : '#D5D5D5', borderWidth: 1, height: Platform.OS == 'android' ? openMason ? 232 : 35 : 'auto', borderRadius: 5 }}>

                            <DropDownPicker
                                listMode='SCROLLVIEW'
                                scrollViewProps={{
                                    nestedScrollEnabled: true,
                                }}
                                style={{
                                    backgroundColor: '#fff00000',
                                    borderColor: openMason ? '#D5D5D5' : '#00000000',
                                    borderRadius: 5,
                                    width: '100%',
                                    height: 35,
                                    paddingBottom: 15

                                }}
                                open={openMason}
                                value={valueMason}
                                items={masonList}
                                setOpen={setOpenMason}
                                setValue={setValueMason}
                                placeholder={textValue.Select_Mason}
                                searchable={true}
                                onChangeValue={(value) => {
                                    if (value) {
                                        setRewardHistory([])
                                        setCopyrewardHistory([])
                                        get_rewards(1,value,[])
                                    }
                                }}
                                textStyle={{
                                    fontSize: 14,
                                }}
                                dropDownContainerStyle={{
                                    borderWidth: 1,
                                    borderColor: '#a8a8a8',
                                    zIndex: 99999999,
                                    elevation: 1000,
                                    width: '100%',
                                    backgroundColor: '#fff',
                                    marginTop: -15

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
                    </View> : null}

                    {user_details?.data?.role != 2 ? <Text style={{ textAlign: 'center', marginTop: 10, marginBottom: 5, fontSize: 16, fontWeight: '700' }}>{total_point == null || total_point == 'null' ? convertForShowData(textValue.NET_POINTS)+' : ' + convertForShowData('0') : convertForShowData(textValue.NET_POINTS)+' : ' + convertForShowData(total_point)}</Text> : null}

                    <FlatList
                        data={rewardHistory}
                        renderItem={renderItem}
                        ListFooterComponent={renderFooter}
                        keyExtractor={(item, index) => {
                            return index
                        }}
                    />
                    {user_details?.data?.role == 2 ? <>
                        {total_point > 0 && visibleRedeem !== '0' ? <View style={styles._btn_view}>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                onPress={() => {
                                    props.navigation.navigate('Gift', { user_type: 'mason', obj: user_details })
                                }}
                                style={styles._btn_view._btn}>
                                <Text style={styles._btn_view._btn._txt}>{convertForShowData(textValue.Redeem_Now)}</Text>
                            </TouchableOpacity>

                        </View> : null}
                    </> : null}

                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default RewardDetails