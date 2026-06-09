import React, { useEffect, useState } from 'react'
import { Text, View, Image, TouchableOpacity, Platform, Keyboard, TextInput } from 'react-native'
import AsyncStorage from '@react-native-async-storage/async-storage'
import { useDispatch } from 'react-redux'
import Toast from 'react-native-toast-message'
import DeviceInfo from 'react-native-device-info'
import styles from './OtpStyle'
import { postApi, postApiWithHeader, postApiWithSingleHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import { updateData } from '../../../redux/reducer/userInfoReducer'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import DataStore from '../../../helper/constants/DataStore'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const Otp = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()
    const dispatch = useDispatch()

    const [loading, setLoading] = useState(false)
    const [otp, setOtp] = useState(['', '', '', ''])
    const inputRefs = []

    useEffect(() => {
        if (otp.every((digit) => digit !== '')) {
            Keyboard.dismiss()
        }
    }, [otp])

    const handleInputChange = (text, index) => {
        const newOtp = [...otp]
        newOtp[index] = text
        setOtp(newOtp)
        if (text && index < 3) {
            inputRefs[index + 1].focus()
        }
    }

    const handleKeyPress = (e, index) => {
        if (e.nativeEvent.key === 'Backspace' && index > 0 && !otp[index]) {
            inputRefs[index - 1].focus()
        }
    }

  const validate_otp = async () => {
        const Otp = otp.join('')
        let deviceName = await DeviceInfo.getDeviceName()
        let appVersion = await DeviceInfo.getVersion()
        const token = await AsyncStorage.getItem('firebase_token');
        //console.log("token----------->",JSON.stringify(token))
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('phone', convertForUploadData(props.route.params.phone))
        formData.append('otp', convertForUploadData(Otp))
        formData.append('device_name', deviceName)
        formData.append('device_type', Platform.OS === 'ios' ? 'ios' : 'Android')
        formData.append('app_version', appVersion)
        formData.append('preferred_app_lang', selectedLanguage())
        formData.append('fcm_token', token)
        postApiWithSingleHeader(constants.login, formData)
            .then(response => {
                if (response.data.status) {
                    showToast('success', messageList.success, response.data.msg)
                    storeData(response.data)
                } else {
                    setLoading(false)
                    showToast('error', messageList.error, response.data.msg)
                }
            })
            .catch(err => {
 
                setLoading(false)
                showToast('error', messageList.error, messageList.t4)
            })
    }

    const sendOtpApi = async () => {
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('phone', convertForUploadData(props.route.params.phone))
        formData.append('otp_purpose', 'login_mason_te')
        formData.append('preferred_app_lang', selectedLanguage())
        postApi(constants.send_otp, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    showToast('success', messageList.success, response.data.msg)
                } else {
                    showToast('error', messageList.error, response.data.msg)
                }
            })
            .catch(err => {

                setLoading(false)
                showToast('error', messageList.error, messageList.t4)
            })
    }

    const form_validation = () => {
        const Otp = otp.join('')
        if (Otp == '') {
            showToast('error', messageList.error, messageList.t10)
        } else if (Otp.length != 4) {
            showToast('error', messageList.error, messageList.t11)
        } else {
            setLoading(true)
            validate_otp()
        }
    }

    const showToast = (type, title, msg) => {
        Toast.show({
            type: type,
            text1: title,
            text2: msg,
            text2NumberOfLines: 2
        })
    }

    const storeData = async (value) => {
        try {
            await AsyncStorage.setItem('user_info', JSON.stringify(value))
            await AsyncStorage.setItem('access_token', value.access_token)
            dispatch(
                updateData({ role: value.data.role == 2 ? 'mason' : 'te' }),
            )
            updateLanguage(value.access_token)
        } catch (e) { }
    }

    const updateLanguage = async () => {
        var a = ''

        switch (DataStore.language) {
            case 'English':
                a = 'en'
                break
            case 'Hindi':
                a = 'hi'
                break
            case 'Assamese':
                a = 'as'
                break
            case 'Bengali':
                a = 'bn'
                break
            default:
                a = 'en'
        }

        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('preferred_app_lang', a)
        postApiWithHeader(constants.language_change, formData)
            .then(response => {
                props.navigation.navigate('Verified')
            })
            .catch(err => {
                props.navigation.navigate('Verified')
            })
    }

    const secure_mobile_no = () => {
        let mobile_no = props?.route?.params?.phone
        return mobile_no?.substring(0, 4)
    }

    const secure_mobile_last_two_digit = () => {
        let mobile_no = props?.route?.params?.phone
        return mobile_no?.substring(10, 8)
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={{ width: '100%', height: '100%', flexDirection: 'column', backgroundColor: '#ee1d23' }}>
                <View style={{ width: '100%', flex: 1, alignItems: 'center', justifyContent: 'center' }} >
                    <Image source={Icons.app_icon} style={{ width: '60%', resizeMode: 'contain' }} />
                </View>
                <View style={{ width: '100%', paddingHorizontal: 20, paddingVertical: 30, backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                    <View style={styles._upperView}>
                        <View style={{ alignItems: 'center' }}>
                            <Text style={{ fontSize: 24, fontWeight: 800, color: '#000' }}>{convertForShowData(textValue.Verify_Code)}</Text>
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#7B7B7B', fontWeight: 400, textAlign: 'center' }}>
                                {convertForShowData(textValue.otp_message_1) + ' '}
                                <Text style={{ fontSize: 16, color: '#000000', fontWeight: 500 }}>
                                    {convertForShowData('(+91) ' + secure_mobile_no() + '****' + secure_mobile_last_two_digit()) + ' '}
                                </Text>
                                {convertForShowData(textValue.otp_message_2)}
                            </Text>
                            <View style={{ height: 25 }} />
                        </View>
                    </View>
                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center' }}>
                        <View style={styles._otp_input_view}>
                            {otp.map((digit, index) => (
                                <TextInput
                                    key={index}
                                    style={styles._otp_input_view._otp_input}
                                    value={convertForShowData(digit)}
                                    onChangeText={(text) => handleInputChange(text, index)}
                                    keyboardType='number-pad'
                                    maxLength={1}
                                    ref={(ref) => (inputRefs[index] = ref)}
                                    onKeyPress={(e) => handleKeyPress(e, index)}
                                />
                            ))}
                        </View>
                    </View>
                    <View style={{ height: 25 }} />
                    <View style={styles._upperView}>
                        <View style={{ alignItems: 'center' }}>
                            <Text style={{ fontSize: 16, color: '#6A6A6A', fontWeight: 500 }}>{convertForShowData(textValue.Didnt_receive_an_OTP)}</Text>
                            <View style={{ height: 5 }} />
                            <TouchableOpacity activeOpacity={0.8} onPress={() => { sendOtpApi() }}>
                                <Text style={styles._btn_section._resend_btn}>{convertForShowData(textValue.Resend_OTP)}</Text>
                            </TouchableOpacity>
                            <View style={{ height: 25 }} />
                        </View>
                    </View>
                    <TouchableOpacity onPress={() => { form_validation() }} style={styles._lowerView._loginBtn}>
                        <Text style={styles._lowerView._loginBtn._txt}>{convertForShowData(textValue.Verify)}</Text>
                    </TouchableOpacity>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default Otp