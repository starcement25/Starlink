import React, { useEffect, useState } from 'react'
import { Text, View, TouchableOpacity, SafeAreaView, Image, Dimensions } from 'react-native'
import { DrawerContentScrollView, DrawerItemList } from '@react-navigation/drawer'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import DeviceInfo from 'react-native-device-info'
import moment from 'moment'

import { getApiWithHeader, postApiWithHeader } from '../../helper/http/Api'
import constants from '../../helper/constants/Constants'
import Loader from '../loader/Loader'
import useMessageList from '../../helper/constants/useMessageList'
import useTextValue from '../../helper/constants/useTextValue'
import Icons from '../../helper/image/ImageList'
import selectedLanguage from '../../helper/constants/LanguageSelect'
import DataStore from '../../helper/constants/DataStore'
import { convertForShowData } from '../../helper/constants/NumberConverter'
import ImagePath from '../../image/ImagePath'
import { useDeleteVisible } from '../../helper/constants/DeleteVisibleContext'

const seceenWidth = Dimensions.get('screen').width

const CustomDrawer = props => {
  const textValue = useTextValue()
  const messageList = useMessageList()

  const [userInfo, setUserInfo] = useState('')
  const [loader, setLoader] = useState(false)
  const [version, setVersion] = useState('')
  const [isExitPopup, setExitPopup] = useState(false)
const { deleteVisible: isDeleteVisible, setDeleteVisible } = useDeleteVisible();

  useEffect(() => {
    my_profile()
    get_app_version()
    const focusListener = props.navigation.addListener('state', () => { })
    return focusListener
  }, [props.navigation])
  
  useEffect(() => {
    my_profile()
  }, [DataStore.language])
useEffect(() => {
    // Only for cold start — hydrate context from AsyncStorage on first load
    const loadValue = async () => {
      const value = await AsyncStorage.getItem("deleteVisible");
      if (value !== null) {
        setDeleteVisible(JSON.parse(value));
      }
    };
    loadValue();
  }, []);

  const get_app_version = async () => {
    let appVersion = DeviceInfo.getVersion()
    setVersion(appVersion)
  }

  const my_profile = () => {
    getApiWithHeader(constants.my_profile)
      .then(response => {
        if (response.data.status) {
          setUserInfo(response.data.data)
        }
      })
      .catch(err => { })
  }

  const _logout = async () => {
    props.navigation.toggleDrawer()
    try {
      const keys = ['user_info', 'access_token']
      await AsyncStorage.multiRemove(keys)
      props.navigation.reset({
        index: 0,
        routes: [{ name: 'AuthStack' }],
      })
    } catch (e) { }
  }

  const _user_logout = async () => {
    setLoader(true)
    getApiWithHeader(constants.logout + '?preferred_app_lang=' + selectedLanguage())
      .then(response => {
        setLoader(false)
        if (response?.data?.status) {
          _logout()
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
        setLoader(false)
      })
  }

  const _delete_account = async () => {
    setExitPopup(true)
  }

  const delete_user = async () => {
    postApiWithHeader(constants.delete_user + '?preferred_app_lang=' + selectedLanguage())
      .then(response => {
        if (response?.data?.status) {
          props.navigation.toggleDrawer()
          showToast('success', response.data.msg)
          goto_auth()
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
        setLoader(false)
      })
  }

  const showToast = (type, msg) => {
    Toast.show({ type: type, text2: msg, text2NumberOfLines: 2 })
  }

  const goto_auth = async () => {
    try {
      await AsyncStorage.clear()
      props.navigation.reset({
        index: 0,
        routes: [{ name: 'AuthStack' }],
      })
    } catch (e) { }
  }

  const delete_user_alert_handle = async () => {
    getApiWithHeader(constants.my_profile + '?preferred_app_lang=' + selectedLanguage())
      .then(response => {
        if (response?.data?.status) {
          _delete_account()
        } else {
          if (response?.data?.status_code == 401) {
            showToast('error', response?.data?.message)
            _logout()
          } else {
            showToast('error', response?.data?.msg)
          }
        }
      })
      .catch(err => { })
  }

  return (
    <SafeAreaView style={{ flex: 1, backgroundColor: '#0000', position: 'relative' }}>
      <View style={{ width: '100%', height: '100%', borderTopRightRadius: 30, borderBottomRightRadius: 30, backgroundColor: '#fff' }}>
        <View style={{ width: '100%', height: '100%', flexDirection: 'column-reverse' }}>
          <Image source={ImagePath.Design1} style={{ width: '65%', height: 100, resizeMode: 'contain' }} />
        </View>
        <View style={{ width: '100%', height: '100%', position: 'absolute' }}>
          <DrawerContentScrollView
            {...props}
            contentContainerStyle={{ backgroundColor: 'transparent' }}>
            <View style={{ height: 'auto', marginTop: -4, width: '100%', backgroundColor: '#FFF0F1', flexDirection: 'row', alignItems: 'center', borderTopRightRadius: 30, paddingHorizontal: 15, paddingVertical: 20 }}>
              <View style={{ width: 75, height: 75, padding: 2, borderWidth: 1, borderColor: '#EE1D23', borderRadius: 40 }}>
                <Image style={{ height: '100%', width: '100%', resizeMode: 'cover', borderRadius: 75 }} source={userInfo?.profile_pic == null ? Icons.avatar : { uri: userInfo?.profile_pic + '?t=' + moment().valueOf() }} />
                {/* + '?t=' + moment().valueOf() */}
              </View>
              <View style={{ width: 10 }} />
              <View style={{ flex: 1, flexDirection: 'column' }}>
                <Text style={{ fontSize: 16, fontWeight: '600', color: '#000000', }}>
                  {convertForShowData(userInfo?.name)}
                </Text>
                <View style={{ height: 3 }} />
                <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                  <Image style={{ width: 10, height: 10 }} source={ImagePath.CallIconRed} />
                  <View style={{ width: 5 }} />
                  <Text style={{ fontSize: 16, fontWeight: '600', color: '#000', }}>
                    {convertForShowData('+91 ' + userInfo?.phone)}
                  </Text>
                </View>
              </View>
            </View>
            <DrawerItemList {...props} />
            {isDeleteVisible && <TouchableOpacity style={{ alignItems: 'center', marginTop: 10 }} activeOpacity={0.6} onPress={() => { delete_user_alert_handle() }}>
              <View style={{ backgroundColor: 'transparent', height: 42, width: '83%', borderRadius: 4, alignItems: 'center', flexDirection: 'row', paddingLeft: 8, }}>
                <View style={{ backgroundColor: '#fff', paddingRight: 6, borderRadius: 5 }}>
                  <Image style={{ width: 16, height: 16, tintColor: '#F8373C' }} source={Icons.delete_1} />
                </View>
                <Text style={{ fontWeight: '400', color: '#888', marginLeft: 8 }}>
                  {convertForShowData(textValue.Delete_Account)}
                </Text>
              </View>
            </TouchableOpacity>}
            <View style={{ height: 3 }} />
            <TouchableOpacity style={{ alignItems: 'center', marginTop: 10 }} activeOpacity={0.6} onPress={() => { _user_logout() }}>
              <View style={{ backgroundColor: 'transparent', height: 42, width: '83%', borderRadius: 4, alignItems: 'center', flexDirection: 'row', paddingLeft: 8, }}>
                <View style={{ backgroundColor: '#fff',paddingRight: 6, borderRadius: 5 }}>
                  <Image style={{ width: 16, height: 16, resizeMode: 'contain' }} source={ImagePath.LeftMenuLogoutIcon} />
                </View>
                <Text style={{ fontWeight: '400', color: '#888', marginLeft: 8 }}>
                  {convertForShowData(textValue.Logout)}
                </Text>
              </View>
            </TouchableOpacity>
            <View style={{ height: 75, width: '100%', justifyContent: 'center' }}>
              <Text style={{ color: '#F8373C', textAlign: 'center', fontSize: 10 }}>{convertForShowData(textValue.Version + ' ')}{convertForShowData(version)}</Text>
            </View>
          </DrawerContentScrollView>
        </View>
      </View>

      {loader ? <Loader></Loader> : null}

      {isExitPopup ? <TouchableOpacity onPress={() => { }} style={{ width: seceenWidth, height: '100%', position: 'absolute', backgroundColor: '#0006', alignItems: 'center', justifyContent: 'center' }}>
        <View style={{ width: '80%', backgroundColor: '#fff', padding: 30, borderRadius: 20 }}>
          {isDeleteVisible && <Text style={{ fontSize: 20, fontWeight: '600', color: '#000' }}>{messageList.t2 + "kkk"}</Text>}
          <View style={{ height: 15 }} />
          <Text style={{ fontSize: 14, color: '#5A5A5A' }}>{messageList.t3}</Text>
          <View style={{ height: 30 }} />
          <View style={{ width: '100%', flexDirection: 'row' }}>
            <View style={{ flex: 1 }} />
            <TouchableOpacity onPress={() => setExitPopup(false)} style={{ width: 100, height: 35, borderWidth: 1, borderRadius: 10, backgroundColor: '#FFF5F6', borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
              <Text style={{ color: '#5A5A5A', fontSize: 16, fontWeight: '500', textTransform: 'uppercase' }}>{textValue.Cancel}</Text>
            </TouchableOpacity>
            <View style={{ width: 10 }} />
            <TouchableOpacity onPress={() => {
              setLoader(true)
              delete_user()
            }} style={{ width: 100, height: 35, borderWidth: 1, borderRadius: 10, backgroundColor: '#EE1D23', borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
              <Text style={{ color: '#FFFFFF', fontSize: 16, fontWeight: '500', textTransform: 'uppercase' }}>{textValue.Delete}</Text>
            </TouchableOpacity>
          </View>
        </View>
      </TouchableOpacity> : null}
    </SafeAreaView>
  )
}

export default CustomDrawer