import React, { useState, useEffect, useRef } from 'react'
import { Text, View, TouchableOpacity, Modal, ScrollView, SafeAreaView, Keyboard } from 'react-native'
import { TextInput } from 'react-native-gesture-handler'
import Icon from 'react-native-vector-icons/MaterialCommunityIcons'
import styles from './AddressStyle'
import Icon2 from 'react-native-vector-icons/AntDesign'
import Loader from '../../components/loader/Loader'
import { postApi } from '../../helper/http/Api'
import constants from '../../helper/constants/Constants'
import useTextValue from '../../helper/constants/useTextValue'
import useMessageList from '../../helper/constants/useMessageList'
import selectedLanguage from '../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../helper/constants/NumberConverter'
import Toast from 'react-native-toast-message'

let infoType = ''

export default function AddressModal(props) {
    const textValue = useTextValue()
    const messageList = useMessageList()
    const inputRefs = useRef([])

    // controlled by parent: default to false if not provided
    const visibleFromParent = !!props.visible

    const [inputEditable, setInputEditable] = useState(true)
    const [address1, setAddress1] = useState('')
    const [address2, setAddress2] = useState('')
    const [city, setCity] = useState('')
    const [district, setDistrict] = useState('')
    const [state, setState] = useState('')
    const [email, setEmail] = useState('')
    const [country, setCountry] = useState('')
    const [pin, setPin] = useState('')
    const [modalVisibility, setModalVisibility] = useState(visibleFromParent)
    const [loading, setLoading] = useState(false)
    const [infoView, setInfoView] = useState(false)
    const [infoTxt, setInfoTxt] = useState('')
    const [otp, setOtp] = useState(['', '', '', ''])

    // keep modalVisibility in sync with parent prop
    useEffect(() => {
        setModalVisibility(visibleFromParent)
        if (visibleFromParent) {
            // initialize fields from props each time the modal opens
            setAddress1(props?.obj?.user_info?.address1 !== 'null' ? props?.obj?.user_info?.address1 : '')
            setAddress2(props?.obj?.user_info?.address2 !== 'null' ? props?.obj?.user_info?.address2 : '')
            setCity(props?.obj?.user_info?.city !== 'null' ? props?.obj?.user_info?.city : '')
            setDistrict(props?.obj?.user_info?.district !== 'null' ? props?.obj?.user_info?.district : '')
            setState(props?.obj?.user_info?.state !== 'null' ? props?.obj?.user_info?.state : '')
            setCountry(props?.obj?.user_info?.country !== 'null' ? props?.obj?.user_info?.country : '')
            setPin(props?.obj?.user_info?.pincode !== 'null' ? props?.obj?.user_info?.pincode : '')
            setEmail(props?.obj?.user_info?.email !== 'null' ? props?.obj?.user_info?.email : '')
            setInputEditable(true)
            setOtp(['', '', '', '']) // reset OTP inputs
            // trigger OTP send when modal opens
            send_otp()
        }
    }, [visibleFromParent, props?.obj])

    useEffect(() => {
        if (otp.every((digit) => digit !== '')) {
            Keyboard.dismiss()
        }
    }, [otp])

    const handleInputChange = (text, index) => {
        const newOtp = [...otp]
        newOtp[index] = text
        setOtp(newOtp)
        if (text && index < 3 && inputRefs.current[index + 1]) {
            inputRefs.current[index + 1].focus()
        }
    }

    const handleKeyPress = (e, index) => {
        if (e.nativeEvent.key === 'Backspace' && index > 0 && !otp[index]) {
            if (inputRefs.current[index - 1]) inputRefs.current[index - 1].focus()
        }
    }

    const send_otp = async () => {
        try {
            setLoading(true)
            var FormData = require('form-data')
            var formData = new FormData()
            formData.append('phone', convertForUploadData(props?.obj?.user_info?.phone))
            formData.append('otp_purpose', 'gift_redemption')
            formData.append('preferred_app_lang', selectedLanguage())

            const response = await postApi(constants.send_otp_to_new_number, formData)
            setLoading(false)
            if (response.data?.status) {
                setInfoTxt(response.data.msg)
                infoType = 'success'
                setInfoView(true)
                setTimeout(() => setInfoView(false), 2000)
            } else {
                setInfoTxt(response.data.msg)
                infoType = 'error'
                setInfoView(true)
                setTimeout(() => setInfoView(false), 2000)
            }
        } catch (err) {
            setLoading(false)
            infoType = 'error'
            setInfoTxt(messageList.t4)
            setInfoView(true)
            setTimeout(() => setInfoView(false), 2000)
        }
    }

    const isValidEmail = (email) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        return regex.test(email)
    }

    const checkData = () => {
        if (props.redeemableItem?.is_email_required) {
            if (email == '') {
                Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t47 })
            } else if (!isValidEmail(email)) {
                Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t48 })
            } else {
                verify_otp()
            }
        } else {
            verify_otp()
        }
    }

    const verify_otp = async () => {
        try {
            setLoading(true)
            var FormData = require('form-data')
            var formData = new FormData()
            formData.append('phone', convertForUploadData(props?.obj?.user_info?.phone))
            formData.append('otp', convertForUploadData(otp.join('')))
            formData.append('preferred_app_lang', selectedLanguage())

            const response = await postApi(constants.verify_phone, formData)
            if (response.data?.status) {
                //console.log("abc-----", response.data?.status)
                requestForTranslateTextLanguage()
            } else {
                setLoading(false)
                infoType = 'error'
                setInfoTxt(response.data.msg)
                setInfoView(true)
                setTimeout(() => setInfoView(false), 2000)
            }
        } catch (err) {
            setLoading(false)
            infoType = 'error'
            setInfoTxt(messageList.t4)
            setInfoView(true)
            setTimeout(() => setInfoView(false), 2000)
        }
    }

    const requestForTranslateTextLanguage = async () => {
        setLoading(true)
        var arr = []
        arr.push(address1 ?? '')
        arr.push(address2 ?? '')
        arr.push(city ?? '')
        arr.push(district ?? '')
        arr.push(state ?? '')
        arr.push(country ?? '')
        arr.push(pin ?? '')

        const myHeaders = new Headers()
        myHeaders.append('Content-Type', 'application/json')

        const raw = JSON.stringify({ 'q': arr, 'target': 'en' })
        const requestOptions = { method: 'POST', headers: myHeaders, body: raw, redirect: 'follow' }

        try {
            const response = await fetch('https://translation.googleapis.com/language/translate/v2?key=AIzaSyBGLsoao9R0m9mEYxVrvNWnSu2ullebn2I', requestOptions)
            const result = await response.json()
            setLoading(false)
            // close modal and return translated address
            setModalVisibility(false)
            props.onClose && props.onClose({
                address: {
                    address1: result.data.translations[0].translatedText,
                    address2: result.data.translations[1].translatedText,
                    city: result.data.translations[2].translatedText,
                    district: result.data.translations[3].translatedText,
                    state: result.data.translations[4].translatedText,
                    country: result.data.translations[5].translatedText,
                    pincode: result.data.translations[6].translatedText,
                    is_email_required: props.redeemableItem?.is_email_required,
                    email: email
                },
                status: inputEditable
            })
        } catch (err) {
            setLoading(false)
            setModalVisibility(false)
            props.onClose && props.onClose({
                address: {
                    address1: arr[0],
                    address2: arr[1],
                    city: arr[2],
                    district: arr[3],
                    state: arr[4],
                    country: arr[5],
                    pincode: arr[6],
                    is_email_required: props.redeemableItem?.is_email_required,
                    email: email
                },
                status: inputEditable
            })
        }
    }

    // Close handler used by close button or Android back
    const handleClose = () => {
        setModalVisibility(false)
        props.onClose && props.onClose({ address: 'close', status: inputEditable })
    }

    return (
        <View style={styles.container}>
            <Modal
                animationType='fade'
                transparent={true}
                visible={modalVisibility}
                onRequestClose={handleClose}>
                <SafeAreaView style={styles._address_view}>
                    <View style={styles._view}>
                        <ScrollView style={{ width: '100%' }}>
                            <Text style={styles._view._header_txt}>{convertForShowData(textValue.DELIVERY_ADDRESS)}</Text>
                            <View style={styles._view._checkbox_txt}>
                                <Text style={styles._view._checkbox_txt._txt}>{convertForShowData(textValue.Change_delivery_address)}</Text>
                                <TouchableOpacity activeOpacity={0.8} onPress={() => { setInputEditable(!inputEditable) }}>
                                    <Icon name={inputEditable ? 'checkbox-marked' : 'checkbox-blank-outline'} size={25} color='#900' />
                                </TouchableOpacity>
                            </View>
                            {props.redeemableItem?.is_email_required ? <TextInput
                                placeholderTextColor='#a8a8a8'
                                placeholder={"E-mail I'd (Mandatary)"}
                                style={styles._view._text_input}
                                value={email}
                                onChangeText={text => { setEmail(text) }}
                                editable={inputEditable}
                                maxLength={250} /> : null}
                            <TextInput
                                placeholderTextColor='#a8a8a8'
                                placeholder={textValue.Address_1}
                                style={styles._view._text_input}
                                onChangeText={text => { setAddress1(text) }}
                                value={convertForShowData(address1)}
                                editable={inputEditable}
                                multiline={true}
                                maxLength={250} />
                            <TextInput
                                placeholderTextColor='#a8a8a8'
                                placeholder={textValue.Address_2}
                                style={styles._view._text_input}
                                onChangeText={text => { setAddress2(text) }}
                                value={convertForShowData(address2)}
                                editable={inputEditable}
                                multiline={true}
                                maxLength={250} />
                            <TextInput
                                placeholderTextColor='#a8a8a8'
                                placeholder={textValue.City}
                                style={styles._view._text_input}
                                onChangeText={text => { setCity(text) }}
                                value={convertForShowData(city)}
                                editable={inputEditable}
                                multiline={true}
                                maxLength={250} />
                            <TextInput
                                placeholderTextColor='#a8a8a8'
                                placeholder={textValue.District}
                                style={styles._view._text_input}
                                onChangeText={text => { setDistrict(text) }}
                                value={convertForShowData(district)}
                                editable={inputEditable}
                                multiline={true}
                                maxLength={250} />
                            <TextInput
                                placeholderTextColor='#a8a8a8'
                                placeholder={textValue.State}
                                style={styles._view._text_input}
                                onChangeText={text => { setState(text) }}
                                value={convertForShowData(state)}
                                editable={inputEditable}
                                multiline={true}
                                maxLength={250} />
                            <TextInput
                                placeholderTextColor='#a8a8a8'
                                placeholder={textValue.Country}
                                style={styles._view._text_input}
                                onChangeText={text => { setCountry(text) }}
                                value={convertForShowData(country)}
                                editable={inputEditable}
                                multiline={true}
                                maxLength={250} />
                            <TextInput
                                placeholderTextColor='#a8a8a8'
                                placeholder={textValue.Pin}
                                keyboardType='number-pad'
                                style={styles._view._text_input}
                                onChangeText={text => { setPin(text) }}
                                value={convertForShowData(pin)}
                                editable={inputEditable}
                                multiline={true}
                                maxLength={250} />
                            <Text style={{ fontSize: 18, fontWeight: '700', marginTop: 10, marginBottom: 5, textAlign: 'center' }}>{convertForShowData(textValue.ENTER_OTP)}</Text>
                            <View style={{ alignItems: 'center' }}>
                                <View style={{ width: '75%', height: 60, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                    {otp.map((digit, index) => (
                                        <TextInput
                                            key={index}
                                            style={{ borderColor: '#a8a8a8', borderWidth: 1, borderRadius: 5, textAlign: 'center', fontSize: 18, fontWeight: '600', width: 40, height: 40, backgroundColor: '#fff', margin: 10, color: '#000' }}
                                            value={convertForShowData(digit)}
                                            onChangeText={(text) => handleInputChange(text, index)}
                                            keyboardType='number-pad'
                                            maxLength={1}
                                            ref={(ref) => (inputRefs.current[index] = ref)}
                                            onKeyPress={(e) => handleKeyPress(e, index)}
                                        />
                                    ))}
                                </View>
                                <View style={styles._btn_section}>
                                    <Text style={styles._btn_section._txt}>{convertForShowData(textValue.Didnt_receive_an_OTP)}</Text>
                                    <TouchableOpacity activeOpacity={0.8} onPress={() => { send_otp() }}>
                                        <Text style={styles._btn_section._resend_btn}>{convertForShowData(textValue.Resend_OTP)}</Text>
                                    </TouchableOpacity>
                                </View>
                                <TouchableOpacity activeOpacity={0.8} style={styles._view._btn} onPress={() => { verify_otp() }}>
                                    <Text style={styles._view._btn._txt}>{convertForShowData(textValue.Confirm)}</Text>
                                </TouchableOpacity>
                            </View>
                        </ScrollView>
                        <TouchableOpacity activeOpacity={0.8} style={styles._view._close_btn} onPress={handleClose}>
                            <Icon2 name='closecircle' size={25} color='#ee1d23' />
                        </TouchableOpacity>
                    </View>
                    {infoView ? <View style={{ width: '100%', alignItems: 'center', position: 'absolute', top: '10%' }}>
                        <View style={{ width: '90%', height: 50, backgroundColor: '#fff', justifyContent: 'center', padding: 10, shadowColor: '#000000', shadowOffset: { width: 0, height: 3 }, shadowRadius: 5, shadowOpacity: 1.0, elevation: 5, borderRadius: 8, overflow: 'hidden' }}>
                            <Text numberOfLines={2} style={{ fontSize: 12, fontWeight: '600', marginLeft: 5 }}>{convertForShowData(infoTxt)}</Text>
                            <View style={{ width: 5, height: 50, backgroundColor: infoType == 'success' ? 'green' : 'red', position: 'absolute' }}></View>
                        </View>
                    </View> : null}
                </SafeAreaView>
                {loading ? <Loader /> : null}
                <Toast />
            </Modal >
        </View >
    )
}
