import React, { useEffect, useState } from 'react'
import { SafeAreaView, Text, View, Image, TouchableOpacity, ActivityIndicator, Modal, } from 'react-native'
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

const TeDealerLink = props => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [LiftingState, setLiftingState] = useState('Pending')
    const [listData, setListData] = useState('')
    const [flagEdit, setFlagEdit] = useState('pending')
    const [modalVisible, setModalVisible] = useState(false)
    const [selectedItem, setSlectedItem] = useState('')
    const [accptBtn, setAccptBtn] = useState(true)
    const [rejctBtn, setRejctBtn] = useState(true)
    const [flatlistLoader, setFlatListLoader] = useState(true)

    // useEffect(() => {
    //     apiCalling(1, [], 'Pending')
    // }, [])

    useEffect(() => {
        apiCalling(1, [], LiftingState)
    }, [LiftingState])

    const getLiftingPending = (page_value, value) => {
        let url = `te/get-dealer-linking_requests/0?page=${page_value}&preferred_app_lang=` + selectedLanguage()
        

        getApiWithHeader(url)
            .then(response => {
                if (response.data.status) {
                    

                    setLoading(false)
                    setFlagEdit('pending')
                    var a = value
                    a = [...a, ...response.data.data.lists]
                    setListData(a)
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
        let url = `te/get-dealer-linking_requests/1?page=${page_value}&preferred_app_lang=` + selectedLanguage()
        
        getApiWithHeader(url)
            .then(response => {
                

                if (response.data.status) {
                    setLoading(false)
                    setFlagEdit('approved')
                    var a = value
                    a = [...a, ...response.data.data.lists]
                    setListData(a)
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
        let url = `te/get-dealer-linking_requests/2?page=${page_value}&preferred_app_lang=` + selectedLanguage()
        
        getApiWithHeader(url)
            .then(response => {
                if (response.data.status) {
                    setLoading(false)
                    setFlagEdit('rejected')
                    var a = value
                    a = [...a, ...response.data.data.lists]
                    setListData(a)
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
            <View>
                <View
                    style={{
                        flexDirection: 'row',
                        justifyContent: 'space-between',
                        alignItems: 'center',
                    }}>
                    {item.key !== 'lifting_id' ? <View>
                        <Text style={{ color: '#9A9A9A', fontSize: 14 }}>{convertForShowData(item.key)}</Text>
                    </View> : null}
                    {item.key !== 'lifting_id' ? <View>
                        <Text style={{ color: '#000000', fontSize: 14 }}>
                            {convertForShowData(item.value)}
                        </Text>
                    </View> : null}
                </View>
                {item.key !== 'lifting_id' ? <View style={{ paddingVertical: 3, width: '100%' }}>
                    <View
                        style={{ height: 1, width: '100%', backgroundColor: '#9A9A9A' }}
                    />
                </View> : null}
            </View>
        )
    }

    const renderList = ({ item, index }) => {
        return (
            <View
                style={{
                    padding: 30,
                    backgroundColor: '#F3F3F3',
                    borderRadius: 10,
                    marginVertical: 16,
                }}>
                <FlatList
                    data={item.dataItem}
                    renderItem={renderInnerItem}
                    keyExtractor={(item) => item.key}
                />
                {flagEdit == 'pending' ? (
                    <View
                        style={{
                            paddingTop: 10,
                            flexDirection: 'row',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                        }}>
                        <TouchableOpacity
                            onPress={() => validReject(item)}
                            style={{
                                width: '30%',
                                height: 38,
                                justifyContent: 'center',
                                alignItems: 'center',
                                backgroundColor: '#EE1D23',
                                borderRadius: 18,
                            }}>
                            <Text style={{ color: '#FFFFFF', fontSize: 14, fontWeight: '600' }}>
                                {convertForShowData(textValue.REJECT)}
                            </Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                            onPress={() => {
                                setSlectedItem(item)
                                setModalVisible(true)
                            }}
                            style={{
                                width: '30%',
                                height: 38,
                                justifyContent: 'center',
                                alignItems: 'center',
                                backgroundColor: '#509F39',
                                borderRadius: 18,
                            }}>
                            <Text style={{ color: '#FFFFFF', fontSize: 14, fontWeight: '600' }}>
                                {convertForShowData(textValue.ACCEPT)}
                            </Text>
                        </TouchableOpacity>
                    </View>
                ) : null}
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
            <View style={styles._bgColor}>
                <View style={styles._upperView}>
                    <TouchableOpacity
                        style={styles._upperView._back_btn}
                        onPress={() => props.navigation.navigate('Dashboard')}>
                        <Image
                            style={styles._upperView._back_btn._img}
                            source={Icons.back}
                        />
                    </TouchableOpacity>
                    <View style={{ justifyContent: 'center', alignItems: 'center' }}>
                        <Text style={[styles._upperView._txt, { marginBottom: 20 }]}>
                            {convertForShowData(textValue.DEALER_LINK_REQUEST)}
                        </Text>
                    </View>
                </View>
                <View style={styles._lowerView}>
                    <View style={{ marginBottom: 20 }}>
                        <View style={{ flexDirection: 'row', paddingVertical: 10, justifyContent: 'space-between', backgroundColor: '#696969', alignItems: 'center' }}>
                            <TouchableOpacity
                                onPress={() => {
                                    setLiftingState(() => 'Pending')
                                    setListData([])
                                    apiCalling(1, [], 'Pending')
                                }}
                                style={{ paddingHorizontal: 10 }}>
                                <Text
                                    style={{
                                        paddingBottom: 5,
                                        color: LiftingState == 'Pending' ? '#FFFFFF' : '#C5C5C5',
                                        fontSize: 18,
                                    }}>
                                    {convertForShowData(textValue.Pending)}
                                </Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                onPress={() => {
                                    setLiftingState(() => 'Approved')
                                    setListData([])
                                    apiCalling(1, [], 'Approved')
                                }}
                                style={{ paddingHorizontal: 10 }}>
                                <Text
                                    style={{
                                        paddingBottom: 5,
                                        color: LiftingState == 'Approved' ? '#FFFFFF' : '#C5C5C5',
                                        fontSize: 18,
                                    }}>
                                    {convertForShowData(textValue.Approved)}
                                </Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                onPress={() => {
                                    setLiftingState('Reject')
                                    setListData([])
                                    apiCalling(1, [], 'Reject')
                                }}
                                style={{ paddingHorizontal: 10 }}>
                                <Text
                                    style={{
                                        paddingBottom: 5,
                                        color: LiftingState == 'Reject' ? '#FFFFFF' : '#C5C5C5',
                                        fontSize: 18,
                                    }}>
                                    {convertForShowData(textValue.Rejected)}
                                </Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                    <View style={{ paddingTop: 20, paddingHorizontal: 26, flex: 1 }}>
                        <FlatList
                            data={listData}
                            renderItem={renderList}
                            ListFooterComponent={renderFooter}
                            keyExtractor={item => item}
                        />
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
