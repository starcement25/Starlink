import React, { useEffect, useState } from 'react'
import { ScrollView, Text, TextInput, View, TouchableOpacity, Linking, Platform, Image } from 'react-native'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import Icon from 'react-native-vector-icons/dist/Feather'
import styles from './ContactusStyle'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import Icons from '../../../helper/image/ImageList'

const Contactus = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [contactInfo, setContactInfo] = useState('')
    const [msg, setMsg] = useState('')

    useEffect(() => {
        setLoading(true)
        getData()
    }, [])

    const getData = async () => {
        getApiWithHeader(constants.get_contact + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    setContactInfo(response.data.data)
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

    const showToast = (type, msg) => {
        Toast.show({
            type: type,
            text2: msg,
            text2NumberOfLines: 2
        })
    }

    const form_validation = () => {
        if (msg == '') {
            showToast('error', messageList.t35)
        } else {
            setLoading(true)
            changeLanguage()
        }
    }

    const changeLanguage = () => {
        setLoading(true)
        var arr = [msg]
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
                send_query(result.data.translations[0].translatedText)
            })
            .catch(err => {
                send_query(arr[0])
            })
    }

    const send_query = async (message) => {
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('message', convertForUploadData(message))
        formData.append('preferred_app_lang', selectedLanguage())

        postApiWithHeader(constants.send_query, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    setMsg('')
                } else {
                    showToast('error', response.data.msg)
                }
            })
            .catch(err => {
                setLoading(false)
                showToast('error', messageList.t4)
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

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={{ width: '100%', height: '100%', flexDirection: 'column', backgroundColor: '#FFF' }}>
                <View style={{ width: '100%', height: 100, borderBottomLeftRadius: 25, borderBottomRightRadius: 25, backgroundColor: '#EE1D23' }} />
            </View>
            <View style={{ width: '100%', height: '100%', position: 'absolute', flexDirection: 'column' }}>
                <View style={{ height: Platform.OS == 'ios' ? 25 : 0 }} />
                <View style={{ width: '100%', height: 70 }}>
                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
                        <Text style={styles._upperView._txt}>{convertForShowData(textValue.CONTACT_US)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() =>setTimeout(()=>{
                                props.navigation.goBack()
                            },500)}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 20 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                        <ScrollView style={styles._scroll}>
                        <View style={styles._margin_top}></View>

                        {contactInfo ? <View style={styles._view._txt_input_view}>
                            <View style={[styles._view._txt_input_view._view]}>
                                <Text style={styles._view._txt_input_view._view._floating_txt}>{convertForShowData(textValue.PHONE_NO)}</Text>
                                <TextInput
                                    placeholderTextColor='#21211F'
                                    placeholder='+91 9932123123'
                                    style={styles._view._txt_input_view._view._txt}
                                    value={convertForShowData(contactInfo?.mobile)}
                                    editable={false} />
                                <TouchableOpacity onPress={() => { Linking.openURL(`tel:${contactInfo?.mobile}`) }} style={{ width: 30, height: 30, position: 'absolute', right: 0, justifyContent: 'center', alignItems: 'center' }}>
                                    <Icon name='phone-call' size={20} />
                                </TouchableOpacity>
                                <View style={styles._view._txt_input_view._view._under_line}></View>
                            </View>
                        </View> : null}

                        <Text style={styles._get_in_touch}>{convertForShowData(textValue.Get_In_Touch)}</Text>

                        <View style={[styles._view._input, { height: 120, justifyContent: 'flex-start', padding: 10 }]}>
                            <TextInput
                                placeholderTextColor='#21211F'
                                placeholder={textValue.Message}
                                multiline={true}
                                onChangeText={text => setMsg(text)}
                                value={convertForShowData(msg)}
                                color='#000000' />
                        </View>

                        <TouchableOpacity activeOpacity={0.8} onPress={() => { form_validation() }} style={styles._view._btn}>
                            <Text style={styles._view._btn._txt}>{convertForShowData(textValue.Submit)}</Text>
                        </TouchableOpacity>

                    </ScrollView>
                    </View>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default Contactus