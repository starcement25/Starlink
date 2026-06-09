import React, { useEffect, useState } from 'react'
import { SafeAreaView, Text, View, Image, TouchableOpacity, FlatList, Modal } from 'react-native'
import styles from './GiftStyle'
import { ScrollView } from 'react-native-virtualized-view'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import Gifts from '../../../components/gifts/Gifts'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const places = [
    {
        'id': 1,
        'title': 'Wine',
        'selected': true
    },
    {
        'id': 2,
        'title': 'Beer',
        'selected': false
    },
    {
        'id': 3,
        'title': 'Whiskey',
        'selected': false
    },
    {
        'id': 4,
        'title': 'Tequila',
        'selected': false
    },
    {
        'id': 5,
        'title': 'Cognac',
        'selected': false
    }
]

const Gift = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [gifts, setGifts] = useState('')
    const [arrayList, setArrayList] = useState('')

    const [modalVisible, setModalVisible] = useState(false)
    const [modalType, setModalType] = useState('')
    const [modalMsg, setModalMsg] = useState('')
    const [itemId, setItemId] = useState('')

    useEffect(() => {
        const focusListener = props.navigation.addListener('focus', () => {
            setArrayList(places)
            setLoading(true)
            getData()
        })
        return focusListener
    }, [props.navigation])

    const getData = async () => {
        getApiWithHeader(constants.get_gift_catalogues+'?preferred_app_lang='+selectedLanguage())
            .then(response => {
                setLoading(false)
                
                if (response.data.status) {
                    setGifts(response.data.data)
                } else {
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else {
                        showToast('error', response?.data?.msg)
                    }
                }
            })
            .catch(err => {
                
                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const apply_redeemtion = async (data) => {
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('user_id', data?.user_info?.id)
        formData.append('catalogue_id', data?.data?.id)
        formData.append('redeemed_point', data?.data?.point)
        formData.append('role', data?.user_info?.role)
        formData.append('address1', convertForUploadData(data?.address?.address1))
        formData.append('address2', convertForUploadData(data?.address?.address2))
        formData.append('city', convertForUploadData(data?.address?.city))
        formData.append('district', convertForUploadData(data?.address?.district))
        formData.append('state', convertForUploadData(data?.address?.state))
        formData.append('country', convertForUploadData(data?.address?.country))
        formData.append('pincode', convertForUploadData(data?.address?.pincode))
        formData.append('preferred_app_lang', selectedLanguage())
        postApiWithHeader(constants.apply_redeemtion, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    props.navigation.goBack()
                } else {
                    showToast('error', response.data.msg)
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
            text2NumberOfLines:2
        })
    }

    function modal_view(data) {
        apply_redeemtion(data)
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

    const loaderOnOff=(id)=>{
        setItemId(id)
        setLoading(!loading)
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._bgColor}>
                <View style={styles._upperView}>
                    <Text style={styles._upperView._txt}>{convertForShowData(textValue.GIFT_CATALOGUE)}</Text>
                    <TouchableOpacity style={styles._upperView._back}
                        onPress={() => {
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}
                    >
                        <Image style={styles._upperView._back._img} source={Icons.back} />
                    </TouchableOpacity>
                </View>

                <View style={styles._lowerView}>
                    <ScrollView nestedScrollEnabled={true} style={{ width: '100%', marginTop: '10%', marginBottom: 10 }}>
                        <FlatList
                            data={gifts}
                            renderItem={({ item, index }) => (
                                <View key={item.id}>
                                    <Gifts obj={{
                                        status:item.id==itemId,
                                        data: item,
                                        index:index,
                                        type: props?.route?.params?.user_type,
                                        points: props?.route?.params?.obj?.data?.points,
                                        user_data: props?.route?.params?.obj?.data
                                    }} sendData={modal_view}
                                    loaderOnOff={loaderOnOff}/>
                                </View>
                            )}
                            keyExtractor={item => item.id}
                        />

                    </ScrollView>
                </View>
            </View>
            {loading ? <Loader /> : null}
            <Modal
                animationType='fade'
                transparent={true}
                visible={modalVisible}
                onRequestClose={(res) => {
                    setModalVisible(!modalVisible)
                }}
            >
                <View style={styles._modal}>
                    {modalType == 'info' && (
                        <View style={styles._modal._info_view}>
                            <Text style={styles._modal._info_view._txt}>{convertForShowData(modalMsg)}</Text>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={styles._modal._info_view._btn}
                                onPress={() => {
                                    setModalType('')
                                    setModalVisible(false)
                                }}
                            >
                                <Text style={styles._modal._info_view._btn._txt}>{convertForShowData(textValue.Cancel)}</Text>
                            </TouchableOpacity>
                        </View>
                    )}
                    {modalType == 'confirm' && (
                        <View style={styles._modal._confirm_view}>
                            <Text style={styles._modal._confirm_view._txt}>{convertForShowData(textValue.Are_you_sure_to_redeemed_this_product)}</Text>
                            <View style={styles._modal._confirm_view._btn_section}>
                                <TouchableOpacity
                                    activeOpacity={0.8}
                                    style={styles._modal._confirm_view._btn_section._btn}
                                    onPress={() => {
                                        setModalType('')
                                        setModalVisible(false)
                                    }}
                                >
                                    <Text style={styles._modal._confirm_view._btn_section._btn._txt}>{convertForShowData(textValue.Cancel)}</Text>
                                </TouchableOpacity>
                                <TouchableOpacity
                                    activeOpacity={0.8}
                                    style={styles._modal._confirm_view._btn_section._btn}
                                    onPress={() => {
                                        setModalType('')
                                        setModalVisible(false)
                                    }}
                                >
                                    <Text style={styles._modal._confirm_view._btn_section._btn._txt}>{convertForShowData(textValue.Redeem)}</Text>
                                </TouchableOpacity>
                            </View>
                        </View>
                    )}
                </View>
            </Modal>
        </SafeView>
    )
}

export default Gift