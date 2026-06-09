import React, { useState, useEffect } from 'react'
import {
  Text,
  TextInput,
  View,
  Image,
  Alert,
  BackHandler,
  TouchableOpacity,
  Platform,
  Linking,
} from 'react-native'
import Toast from 'react-native-toast-message'
import DeviceInfo from 'react-native-device-info'
import styles from './LoginStyle'
import Loader from '../../../components/loader/Loader'
import useTextValue from '../../../helper/constants/useTextValue'
import { getApi, postApi, postApiJSON } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import DataStore from '../../../helper/constants/DataStore'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

var backHandler

const Login = (props) => {
  const textValue = useTextValue()
  const messageList = useMessageList()

  const [isExitPopup, setExitPopup] = useState(false)
  const [loading, setLoading] = useState(false)
  const [phone, setPhone] = useState('')

  useEffect(() => {
    const backAction = () => {
      setExitPopup(true)
      return true
    }

    backHandler = BackHandler.addEventListener(
      'hardwareBackPress',
      backAction
    )

    props.navigation.addListener('focus', () => {
      backHandler = BackHandler.addEventListener(
        'hardwareBackPress',
        backAction
      )
    })

    return () => {
      if (backHandler && backHandler.remove) {
        backHandler.remove()
      }
    }
  }, [props.navigation])

  useEffect(() => {
    app_version_check()
  }, [])

  useEffect(() => {
    setPhone(convertForShowData(phone))
  }, [DataStore.language])

  const sendOtpApi = async () => {
    try {
      const payload = {
        phone: convertForUploadData(phone),
        otp_purpose: 'login_mason_te',
        preferred_app_lang: selectedLanguage(),
      }

      //console.log('[Login] sendOtp payload:', { phone: convertForUploadData(phone), otp_purpose: 'login_mason_te', preferred_app_lang: selectedLanguage(), })

      const response = await postApiJSON(constants.send_otp, payload)
      //console.log('[Login] sendOtp response:', response?.data)

      setLoading(false)

      if (response?.data?.status) {
        if (backHandler && backHandler.remove) {
          backHandler.remove()
        }
        showToast('success', messageList.success, response.data.msg)
        props.navigation.navigate('Otp', { phone: phone })
      } else {
        showToast('error', messageList.error, response?.data?.msg || messageList.t4)
      }
    } catch (err) {
      //console.log('[Login] sendOtp error:', err?.response?.data, err?.message)
      setLoading(false)
      showToast('error', messageList.error, messageList.t4)
    }
  }

  const form_validation = () => {
    if (phone === '') {
      showToast('error', messageList.error, messageList.t6)
    } else {
      setLoading(true)
      sendOtpApi()
    }
  }

  const showToast = (type, title, msg) => {
    Toast.show({
      type: type,              
      text1: title,
      text2: msg,
      text2NumberOfLines: 2,
    })
  }

  const app_version_check = async () => {
    let appVersion = DeviceInfo.getVersion()
    getApi(constants.app_version)
      .then(response => {
        if (response.data.status) {
          let version = response.data.data[0]
          let fetch_app_version = Platform.OS === 'ios' ? version.ios : version.android
          if (fetch_app_version != appVersion) {
            if (Platform.OS === 'ios') {
              Alert.alert(messageList.t7, messageList.t9, [
                {
                  text: 'UPDATE', onPress: () => {
                    const link = 'https://apps.apple.com/in/app/star-link/id6446483968'
                    Linking.openURL(link)
                  }
                }
              ])
            } else {
              Alert.alert(messageList.t7, messageList.t8, [
                {
                  text: 'UPDATE', onPress: () => {
                    const link = 'https://play.google.com/store/apps/details?id=com.starlink'
                    Linking.openURL(link)
                  }
                }
              ])
            }
          }
        }
      })
      .catch(err => {
        //console.log('[Login] app_version_check error:', err?.message)
      })
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
      <View style={styles._bgColor}>
        <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
          <View style={{ width: '100%', flex: 1, alignItems: 'center', justifyContent: 'center' }} >
            <Image source={Icons.app_icon} style={{ width: '60%', resizeMode: 'contain' }} />
          </View>
          <View style={{ width: '100%', paddingHorizontal: 20, paddingVertical: 30, backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
            <View style={styles._upperView}>
              <View style={{ alignItems: 'center' }}>
                <Text style={{ fontSize: 24, fontWeight: 800, color: '#000' }}>
                  {convertForShowData(textValue.Login)}
                </Text>
                <View style={{ height: 5 }} />
                <Text style={{ fontSize: 16, color: '#6A6AA', fontWeight: 500 }}>
                  {convertForShowData(textValue.Enter_your_Mobile_number)}
                </Text>
                <View style={{ height: 25 }} />
              </View>
            </View>
            <TouchableOpacity style={styles._lowerView._input}>
              <View style={{ width: 70, height: '100%', backgroundColor: '#ECECEC', flexDirection: 'row' }}>
                <View style={{ width: 20 }} />
                <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'center', borderTopColor: '#00000000', borderBottomColor: '#00000000', borderLeftColor: '#00000000', backgroundColor: '#ECECEC' }}>
                  <Text style={{ color: '#000' }}>{convertForShowData('+91')}</Text>
                </View>
              </View>
              <View style={{ width: 10 }} />
              <TextInput
                style={styles._lowerView._input._input}
                placeholder={textValue.Enter_mobile_number}
                placeholderTextColor='#808080'
                onChangeText={text => setPhone(text)}
                value={convertForShowData(phone)}
                keyboardType='phone-pad'
              />
            </TouchableOpacity>
            <TouchableOpacity onPress={form_validation} style={styles._lowerView._loginBtn}>
              <Text style={styles._lowerView._loginBtn._txt}>
                {convertForShowData(textValue.Next)}
              </Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
      {loading ? <Loader /> : null}
      {isExitPopup ? (
        <TouchableOpacity
          onPress={() => { }}
          style={{
            width: '100%',
            height: '100%',
            position: 'absolute',
            backgroundColor: '#0006',
            alignItems: 'center',
            justifyContent: 'center',
          }}>
          <View style={{ width: '80%', backgroundColor: '#fff', padding: 30, borderRadius: 20 }}>
            <Text style={{ fontSize: 20, fontWeight: '600', color: '#000' }}>
              {messageList.exit_app}
            </Text>
            <View style={{ height: 15 }} />
            <Text style={{ fontSize: 14, color: '#5A5A5A' }}>
              {messageList.t5}
            </Text>
            <View style={{ height: 30 }} />
            <View style={{ width: '100%', flexDirection: 'row' }}>
              <View style={{ flex: 1 }} />
              <TouchableOpacity
                onPress={() => setExitPopup(false)}
                style={{
                  width: 100,
                  height: 35,
                  borderWidth: 1,
                  borderRadius: 10,
                  backgroundColor: '#FFF5F6',
                  borderColor: '#FFDBDD',
                  alignItems: 'center',
                  justifyContent: 'center',
                }}>
                <Text style={{ color: '#5A5A5A', fontSize: 16, fontWeight: '500', textTransform: 'uppercase' }}>
                  {textValue.Cancel}
                </Text>
              </TouchableOpacity>
              <View style={{ width: 10 }} />
              <TouchableOpacity
                onPress={() => {
                  setExitPopup(false)
                  BackHandler.exitApp()
                }}
                style={{
                  width: 100,
                  height: 35,
                  borderWidth: 1,
                  borderRadius: 10,
                  backgroundColor: '#EE1D23',
                  borderColor: '#FFDBDD',
                  alignItems: 'center',
                  justifyContent: 'center',
                }}>
                <Text style={{ color: '#FFFFFF', fontSize: 16, fontWeight: '500', textTransform: 'uppercase' }}>
                  {textValue.YES}
                </Text>
              </TouchableOpacity>
            </View>
          </View>
        </TouchableOpacity>
      ) : null}
    </SafeView>
  )
}

export default Login
