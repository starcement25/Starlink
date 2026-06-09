import React, { useState, useRef, useCallback } from 'react'
import { ScrollView, Text, View, Image, TouchableOpacity, Platform } from 'react-native'
import Toast from 'react-native-toast-message'
import moment from 'moment'
import styles from './DashBoardTeStyle'
import { getApi, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'
import Loader from '../../../components/loader/Loader'

const DashboardTe = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    //For edit
    const [isStartDate, setIsStartDate] = useState(false)
    const [isEndDate, setIsEndDate] = useState(false)
    const [startDate, setStartDate] = useState(textValue.start_date)
    const [endDate, setEndDate] = useState(textValue.end_date)
    const [dashboardDetails, setDashboardDetails] = useState('')
    const [loading, setLoading]= useState(true)

    const url1 = 'te/dashboard/mason?preferred_app_lang=' + selectedLanguage()
    const url2 = 'te/dashboard/mason?status=1&preferred_app_lang=' + selectedLanguage()
    const url3 = 'te/dashboard/lifting?is_verified=1&preferred_app_lang=' + selectedLanguage()
    const url4 = 'te/dashboard/lifting?is_verified=0&preferred_app_lang=' + selectedLanguage()
    const url5 = 'te/dashboard/liftingbags?product_id=1&preferred_app_lang=' + selectedLanguage()
    const url6 = 'te/dashboard/liftingbags?product_id=2&preferred_app_lang=' + selectedLanguage()
    const url7 = 'te/dashboard/mason/netpoint?preferred_app_lang=' + selectedLanguage()
    const url8 = 'te/dashboard/status/gift?preferred_app_lang=' + selectedLanguage()
    const url9 = 'te/dashboard/status/gift?status=0&preferred_app_lang=' + selectedLanguage()
    const url10 = 'te/dashboard/status/gift?status=1&preferred_app_lang=' + selectedLanguage()
    const url11 = 'te/dashboard/status/query?preferred_app_lang=' + selectedLanguage()
    const url12 = 'te/dashboard/status/query?status=1&preferred_app_lang=' + selectedLanguage()
    const url13 = 'te/dashboard/status/query?status=2&preferred_app_lang=' + selectedLanguage()
    const url14 = 'te/dashboard/status/gift?status=2&preferred_app_lang=' + selectedLanguage()
    const url15 = 'te/dashboard/status/gift?status=3&preferred_app_lang=' + selectedLanguage()

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            getTeDetails()
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    const getTeDetails = async () => {
        setLoading(true)
        if (startDate == textValue.start_date || endDate == textValue.end_date) {
            await getApiWithHeader(constants.te_dashboard + '?preferred_app_lang=' + selectedLanguage())
                .then(response => {
                    //console.log(response.data.data)

                    if (response.data.status) {
                        setDashboardDetails(response.data.data)
                    }
                })
                .catch(err => {
                    showToast('error', messageList.t4)
                })
        } else {
            await getApiWithHeader(constants.te_dashboard + `?from_date=${startDate}&to_date=${endDate}` + '&preferred_app_lang=' + selectedLanguage())
                .then(response => {
                    if (response.data.status) {
                        setDashboardDetails(response.data.data)
                    }
                })
                .catch(err => {
                    showToast('error', messageList.t4)
                })
        }
        setLoading(false)
    }

    const showToast = (type, msg) => {
        Toast.show({
            type: type,
            text2: msg,
            text2NumberOfLines: 2
        })
    }

    const get_products = async (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        setLoading(true)
        await getApi(constants.get_all_products + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
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
                    get_products(page_value + 1, a)
                } else {
                    if (page_value == 1) { showToast('error', response.data.msg) }
                }
                setLoading(false)
            })
            .catch(err => { setLoading(false)})
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
        if (startDate == textValue.start_date || endDate == textValue.end_date) {
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
            <View style={{ width: '100%', height: '100%', flexDirection: 'column', backgroundColor: '#FFF' }}>
                <View style={{ width: '100%', height: 100, borderBottomLeftRadius: 25, borderBottomRightRadius: 25, backgroundColor: '#EE1D23' }} />
            </View>
            <View style={{ width: '100%', height: '100%', position: 'absolute', flexDirection: 'column' }}>
                <View style={{ height: Platform.OS == 'ios' ? 25 : 0 }} />
                <View style={{ width: '100%', height: 70 }}>
                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
                        <Text style={styles._upperView._txt}>{convertForShowData(textValue.DASHBOARD)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() =>setTimeout(()=>{
                                props.navigation.goBack()
                            },500)}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                        <View style={{ width: '100%', height: 45, flexDirection: 'row' }}>
                            <TouchableOpacity onPress={() => setIsStartDate(true)} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFD5D6', flexDirection: 'row', alignItems: 'center', paddingHorizontal: 10 }}>
                                <Text style={{ color: '#000000', flex: 1 }}>{convertForShowData(startDate)}</Text>
                                <Image source={ImagePath.CalendarIcon} style={{ width: 20, height: 20, resizeMode: 'contain' }} />
                            </TouchableOpacity>
                            <DateTimePickerModal
                                isVisible={isStartDate}
                                mode='date'
                                minimumDate={new Date(1900, 0, 1)}
                                onConfirm={handleConfirmStart}
                                onCancel={hideDatePickerStart}
                                date={new Date()}
                            />
                            <View style={{ width: 10 }} />
                            <TouchableOpacity onPress={() => setIsEndDate(true)} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFD5D6', flexDirection: 'row', alignItems: 'center', paddingHorizontal: 10 }}>
                                <Text style={{ color: '#000000', flex: 1 }}>{convertForShowData(endDate)}</Text>
                                <Image source={ImagePath.CalendarIcon} style={{ width: 20, height: 20, resizeMode: 'contain' }} />
                            </TouchableOpacity>

                            <DateTimePickerModal
                                isVisible={isEndDate}
                                mode='date'
                                onConfirm={handleConfirmEnd}
                                onCancel={hideDatePickerEnd}
                                date={new Date()}
                            />
                        </View>
                        <View style={{ height: 10 }} />
                        <TouchableOpacity style={{ width: '100%', height: 55, borderRadius: 10, borderWidth: 1, borderColor: '#EE1D23', justifyContent: 'center', alignItems: 'center' }} onPress={() => getFilteredData()}>
                            <Text style={{ color: '#EE1D23', fontSize: 20 }}>{convertForShowData(textValue.Submit1)}</Text>
                        </TouchableOpacity>
                        <View style={{ height: 30 }} />
                        {dashboardDetails ? <ScrollView showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                            <View style={{ width: '100%', flexDirection: 'column' }}>
                                <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url1, '1')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Total_Linked_Mason)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.total_linked_mason) < 10 ? convertForShowData('0' + dashboardDetails?.total_linked_mason.toString()) : convertForShowData(dashboardDetails?.total_linked_mason.toString())}</Text>
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url2, '2')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Active_Mason)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.active_mason) < 10 ? convertForShowData('0' + dashboardDetails?.active_mason.toString()) : convertForShowData(dashboardDetails?.active_mason.toString())}</Text>
                                    </TouchableOpacity>
                                </View>
                                <View style={{ height: 10 }} />
                                <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url3, '3')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Verified_Lifting)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.verified_lifting) < 10 ? convertForShowData('0' + dashboardDetails?.verified_lifting.toString()) : convertForShowData(dashboardDetails?.verified_lifting.toString())}</Text>
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url4, '4')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Unverified_Lifting)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.unverified_lifting) < 10 ? convertForShowData('0' + dashboardDetails?.unverified_lifting.toString()) : convertForShowData(dashboardDetails?.unverified_lifting.toString())}</Text>
                                    </TouchableOpacity>
                                </View>
                                <View style={{ height: 10 }} />
                                <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url5, '5')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Total_Ppc_Lifting_Bag)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.total_ppc_lifting_bags) < 10 ? convertForShowData('0' + dashboardDetails?.total_ppc_lifting_bags.toString()) : convertForShowData(dashboardDetails?.total_ppc_lifting_bags.toString())}</Text>
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url6, '6')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Total_Arc_Lifting_Bag)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.total_arc_lifting_bags) < 10 ? convertForShowData('0' + dashboardDetails?.total_arc_lifting_bags.toString()) : convertForShowData(dashboardDetails?.total_arc_lifting_bags.toString())}</Text>
                                    </TouchableOpacity>
                                </View>
                                <View style={{ height: 10 }} />
                                <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url7, '7')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Mason_Net_Point)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.mason_net_points).toFixed(2) < 10 ? convertForShowData('0' + parseInt(dashboardDetails?.mason_net_points).toFixed(2).toString()) : convertForShowData(parseInt(dashboardDetails?.mason_net_points).toFixed(2).toString())}</Text>
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url8, '8')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Gift_Redeemed)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.gift_redeemed) < 10 ? convertForShowData('0' + dashboardDetails?.gift_redeemed.toString()) : convertForShowData(dashboardDetails?.gift_redeemed.toString())}</Text>
                                    </TouchableOpacity>
                                </View>
                                <View style={{ height: 10 }} />
                                <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url9, '9')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Gift_pending)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.gift_pending) < 10 ? convertForShowData('0' + dashboardDetails?.gift_pending.toString()) : convertForShowData(dashboardDetails?.gift_pending.toString())}</Text>
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url15, '15')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Gift_Order_Placed)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.gift_order_placed) < 10 ? convertForShowData('0' + dashboardDetails?.gift_order_placed.toString()) : convertForShowData(dashboardDetails?.gift_order_placed.toString())}</Text>
                                    </TouchableOpacity>
                                </View>
                                <View style={{ height: 10 }} />
                                <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url10, '10')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Gift_Delivered)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.gift_delivered) < 10 ? convertForShowData('0' + dashboardDetails?.gift_delivered.toString()) : convertForShowData(dashboardDetails?.gift_delivered.toString())}</Text>
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url14, '14')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Gift_Rejected)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.gift_rejected) < 10 ? convertForShowData('0' + dashboardDetails?.gift_rejected.toString()) : convertForShowData(dashboardDetails?.gift_rejected.toString())}</Text>
                                    </TouchableOpacity>
                                </View>
                                <View style={{ height: 10 }} />
                                <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url11, '11')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Query_Raised)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.query_raised) < 10 ? convertForShowData('0' + dashboardDetails?.query_raised.toString()) : convertForShowData(dashboardDetails?.query_raised.toString())}</Text>
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url12, '12')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Query_pending)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.query_pending) < 10 ? convertForShowData('0' + dashboardDetails?.query_pending.toString()) : convertForShowData(dashboardDetails?.query_pending.toString())}</Text>
                                    </TouchableOpacity>
                                </View>
                                <View style={{ height: 10 }} />
                                <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <TouchableOpacity onPress={() => getDashBoardDetailsOther(url13, '13')} style={{ flex: 1, height: 120, backgroundColor: '#FFF0F1', borderRadius: 10, borderColor: '#FFDFE1', borderWidth: 1, flexDirection: 'column', padding: 10 }}>
                                        <Text style={{ fontSize: 16, color: '#000000' }}>{convertForShowData(textValue.Query_resolved)}</Text>
                                        <View style={{ flex: 1 }} />
                                        <Text style={{ fontSize: 34, fontWeight: '800', color: '#000000' }}>{parseInt(dashboardDetails?.query_resolved) < 10 ? convertForShowData('0' + dashboardDetails?.query_resolved.toString()) : convertForShowData(dashboardDetails?.query_resolved.toString())}</Text>
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <View style={{ flex: 1, height: 120, backgroundColor: '#FFF0F100', borderRadius: 10, borderColor: '#FFDFE100', borderWidth: 1, flexDirection: 'column', padding: 10 }} />
                                </View>
                            </View>
                        </ScrollView> : null}
                    </View>
                </View>
            </View>
            {loading && <Loader />}
        </SafeView>
    )
}

export default DashboardTe