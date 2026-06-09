import React, { useCallback, useEffect, useRef, useState } from 'react'
import { SafeAreaView, Text, View, Image, TouchableOpacity, ActivityIndicator, Modal, Platform, } from 'react-native'
import styles from './TeDealerLinkStyle'
import { getApiWithHeader, postApiWithHeader } from '../../../helper/http/Api'
import { FlatList } from 'react-native-gesture-handler'
import Constants from '../../../helper/constants/Constants'
import Toast from 'react-native-toast-message'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

const TeDealerLink = props => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [LiftingState, setLiftingState] = useState('Pending')
    const [listData, setListData] = useState('')
    const [pendingListData, setPendingListData] = useState([])
    const [approveListData, setApprovedListData] = useState([])
    const [rejectListData, setRejectListData] = useState([])
    const [flagEdit, setFlagEdit] = useState('pending')
    const [modalVisible, setModalVisible] = useState(false)
    const [selectedItem, setSlectedItem] = useState('')
    const [accptBtn, setAccptBtn] = useState(true)
    const [rejctBtn, setRejctBtn] = useState(true)
    const [flatlistLoader, setFlatListLoader] = useState(true)

    // useEffect(() => {
    //     apiCalling(1, [], 'Pending')
    // }, [])

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            apiCalling(1, [], LiftingState)
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    useEffect(() => {
        apiCalling(1, [], LiftingState)
    }, [LiftingState])

    const getLiftingPending = (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }

        let url = `te/get-dealer-linking_requests/0?page=${page_value}&preferred_app_lang=` + selectedLanguage()


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
                    a = [...a, ...response.data.data.lists]
                    setPendingListData(a)
                    apiCalling(1 + page_value, a, 'Pending')
                } else {
                    setLoading(false)
                    setFlatListLoader(false)
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
                setFlatListLoader(false)
                setLoading(false)
                
            })
    }

    const getLiftingApproved = (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }

        let url = `te/get-dealer-linking_requests/1?page=${page_value}&preferred_app_lang=` + selectedLanguage()

        getApiWithHeader(url)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }

                if (response.data.status) {
                    setLoading(false)
                    setFlagEdit('approved')
                    var a = value
                    a = [...a, ...response.data.data.lists]
                    setApprovedListData(a)
                    apiCalling(1 + page_value, a, 'Approved')
                } else {
                    setLoading(false)
                    setFlatListLoader(false)
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
                setFlatListLoader(false)
                    
            })
    }

    const getRejectedLifting = (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }

        let url = `te/get-dealer-linking_requests/2?page=${page_value}&preferred_app_lang=` + selectedLanguage()

        getApiWithHeader(url)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }

                if (response.data.status) {
                    setLoading(false)
                    setFlagEdit('rejected')
                    var a = value
                    a = [...a, ...response.data.data.lists]
                    setRejectListData(a)
                    apiCalling(1 + page_value, a, 'Reject')
                } else {
                    setLoading(false)
                    setFlatListLoader(false)
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
                setFlatListLoader(false)
                    
            })
    }

    const apiCalling = (page_value, arr, callingFrom) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }

        switch (LiftingState) {
            case 'Pending':
                if (callingFrom == 'Pending') {
                    getLiftingPending(page_value, arr)
                }
                break
            case 'Approved':
                if (callingFrom == 'Approved') {
                    getLiftingApproved(page_value, arr)
                }
                break
            case 'Reject':
                if (callingFrom == 'Reject') {
                    getRejectedLifting(page_value, arr)
                }
                break
        }
    }

    const callAcceptApi = item => {
        let newwData = item.dataItem

        setModalVisible(false)
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('linking_request_id', newwData[0].value)
        formData.append('preferred_app_lang', selectedLanguage())

        postApiWithHeader(Constants.accept_linking, formData)
            .then(response => {
                setLoading(false)
                setAccptBtn(true)
                if (response.data.status) {
                    apiCalling(1, [], 'Pending')
                }
            })
            .catch(err => {
                
                setLoading(false)
                setAccptBtn(true)
                showToast('error', messageList.t4)
            })
    }

    const callRejectApi = (item) => {
        let newwData = item.dataItem
        setModalVisible(false)
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('linking_request_id', newwData[0].value)
        formData.append('preferred_app_lang', selectedLanguage())
        postApiWithHeader(Constants.reject_linking, formData)
            .then(response => {
                setRejctBtn(true)
                setLoading(false)
                if (response.data.status) {
                    apiCalling(1, [], 'Pending')
                }
            })
            .catch(err => {
                
                setLoading(false)
                setRejctBtn(true)
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

    const validAccept = () => {
        setAccptBtn(false)
        if (accptBtn) {
            callAcceptApi(selectedItem)
        }
    }

    const validReject = (item) => {
        setRejctBtn(false)
        if (rejctBtn) {
            callRejectApi(item)
        }
    }

    const renderInnerItem = ({ item, index }) => {
        return (
            <>
                {item.key !== 'lifting_id' && item.key !== 'id' ? <View style={index % 2 == 1 ? { flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' } : { flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{item.key === 'id' ? 'ID' : convertForShowData(item.key)}</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>
                            {convertForShowData(item.value)}
                        </Text>
                    </View>
                </View> : null}
            </>

        )
    }

    const renderList = ({ item, index }) => {
        return (
            <View style={{ width: '100%', flexDirection: 'column' }}>
                <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 10, paddingVertical: 10, borderRadius: 5 }}>
                    <FlatList
                        data={item.dataItem}
                        renderItem={renderInnerItem}
                        keyExtractor={(item) => item.key}
                    />
                    {flagEdit == 'pending' ? <View style={{ height: 10 }}></View> : null}
                    {flagEdit == 'pending' ? (<View style={{ flexDirection: 'row', alignItems: 'center' }}>
                        <TouchableOpacity
                            onPress={() => validReject(item)}
                            style={{ flex: 1, height: 38, justifyContent: 'center', alignItems: 'center', backgroundColor: '#EE1D23', borderRadius: 5, }}>
                            <Text style={{ color: '#FFFFFF', fontSize: 14, fontWeight: '600' }}>
                                {convertForShowData(textValue.REJECT)}
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
                    ) : null}
                </View>
                <View style={{ height: 10 }} />
            </View>
        )
    }

    const renderFooter = () => {
        return !flatlistLoader ? null : (
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
                        <Text style={{
                            fontSize: 20,
                            color: '#fff',
                            fontWeight: '600',
                            marginBottom: 20
                        }}>{convertForShowData(textValue.DEALER_LINK_REQUEST)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {
                            props.navigation.navigate('Dashboard')
                        }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                        <View style={{ flexDirection: 'row', paddingVertical: 10, justifyContent: 'space-between', alignItems: 'center' }}>
                            <TouchableOpacity
                                onPress={() => {
                                    setLiftingState(() => 'Pending')
                                    setPendingListData([])
                                }}
                                style={{ paddingHorizontal: 20, paddingVertical: 10, backgroundColor: LiftingState == 'Pending' ? '#FFF5F6' : '#0000', borderRadius: 5 }}>
                                <Text
                                    style={{
                                        color: LiftingState == 'Pending' ? '#EE1D23' : '#292929',
                                        fontSize: 16,
                                    }}>
                                    {convertForShowData(textValue.Pending)}
                                </Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                onPress={() => {
                                    setLiftingState(() => 'Approved')
                                    setApprovedListData([])
                                }}
                                style={{ paddingHorizontal: 20, paddingVertical: 10, backgroundColor: LiftingState == 'Approved' ? '#FFF5F6' : '#0000', borderRadius: 5 }}>
                                <Text
                                    style={{
                                        color: LiftingState == 'Approved' ? '#EE1D23' : '#292929',
                                        fontSize: 16,
                                    }}>
                                    {convertForShowData(textValue.Approved)}
                                </Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                onPress={() => {
                                    setLiftingState('Reject')
                                    setRejectListData([])
                                }}
                                style={{ paddingHorizontal: 20, paddingVertical: 10, backgroundColor: LiftingState == 'Reject' ? '#FFF5F6' : '#0000', borderRadius: 5 }}>
                                <Text
                                    style={{
                                        color: LiftingState == 'Reject' ? '#EE1D23' : '#292929',
                                        fontSize: 16,
                                    }}>
                                    {convertForShowData(textValue.Rejected)}
                                </Text>
                            </TouchableOpacity>
                        </View>
                        <View style={{ height: 10 }} />
                        {LiftingState == 'Pending' ? <FlatList
                            data={pendingListData}
                            renderItem={renderList}
                            ListFooterComponent={renderFooter}
                            keyExtractor={item => item}
                            showsHorizontalScrollIndicator={false}
                            showsVerticalScrollIndicator={false}
                        /> : null}
                        {LiftingState == 'Approved' ? <FlatList
                            data={approveListData}
                            renderItem={renderList}
                            ListFooterComponent={renderFooter}
                            keyExtractor={item => item}
                            showsHorizontalScrollIndicator={false}
                            showsVerticalScrollIndicator={false}
                        /> : null}
                        {LiftingState == 'Reject' ? <FlatList
                            data={rejectListData}
                            renderItem={renderList}
                            ListFooterComponent={renderFooter}
                            keyExtractor={item => item}
                            showsHorizontalScrollIndicator={false}
                            showsVerticalScrollIndicator={false}
                        /> : null}

                    </View>
                </View>
            </View>

            <Modal
                animationType='slide'
                transparent={true}
                visible={modalVisible}>
                <View style={styles.centeredView}>
                    <View style={styles.modalView}>
                        <Text style={styles.modalText}>{convertForShowData(textValue.Are_you_want_to_accept_the_lifting)}</Text>
                        <View style={{ flexDirection: 'row', justifyContent: 'space-between', paddingTop: 26 }}>
                            <TouchableOpacity onPress={() => setModalVisible(false)} style={{ height: 50, width: 131, justifyContent: 'center', backgroundColor: '#EE1D23', borderRadius: 25, margin: 4, justifyContent: 'center', alignItems: 'center' }}>
                                <Text style={{ color: '#FFFFFF', fontSize: 14 }}>{convertForShowData(textValue.REJECT)}</Text>
                            </TouchableOpacity>
                            <TouchableOpacity onPress={() => validAccept()} style={{ height: 50, width: 131, justifyContent: 'center', backgroundColor: '#509F39', borderRadius: 25, margin: 4, justifyContent: 'center', alignItems: 'center' }}>
                                <Text style={{ color: '#FFFFFF', fontSize: 14 }}>{convertForShowData(textValue.ACCEPT)}</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </Modal>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}
export default TeDealerLink
