import React, { useEffect } from 'react';
import { View, Image, Platform, Alert, Linking } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useDispatch, useSelector } from 'react-redux';
import { updateData } from '../../redux/reducer/userInfoReducer';
import { getApi } from '../../helper/http/Api';
import constants from '../../helper/constants/Constants';
import DataStore from '../../helper/constants/DataStore';
import ImagePath from '../../image/ImagePath';
import DeviceInfo from 'react-native-device-info';
import useMessageList from '../../helper/constants/useMessageList';
import messaging from '@react-native-firebase/messaging';

const Splash = ({ navigation }) => {
  const user = useSelector(state => state.user);
  const dispatch = useDispatch();
  const messageList = useMessageList();
  let hasNavigated = false;

  const safeNavigate = (route) => {
  if (hasNavigated) return;
  hasNavigated = true;

  navigation.reset({
    index: 0,
    routes: [{ name: route }],
  });
};

  // Run once on mount
  useEffect(() => {
    //console.log('[Splash] mounted');
    fadeIn();
    requestUserPermission().catch(err => {
      //console.log('[Splash] requestUserPermission error:', err);
    });
  }, []);

  // Ask notification permission (Android 13+)
  async function requestUserPermission() {
    try {
      if (Platform.OS === 'android' && Platform.Version >= 33) {
        //console.log('[Splash] requesting notification permission');
        const authStatus = await messaging().requestPermission();
        const enabled =
          authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
          authStatus === messaging.AuthorizationStatus.PROVISIONAL;

        if (enabled) {
          //console.log('Notification permission granted.');
          await getFCMToken();
        } else {
          //console.log('Notification permission denied.');
        }
      } else {
        // Older Android / iOS – still fetch token if needed
        await getFCMToken();
      }
    } catch (e) {
      //console.log('[Splash] requestUserPermission catch:', e);
    }
  }

  async function getFCMToken() {
    try {
      const token = await messaging().getToken();
      //console.log('FCM Token:', token);
       try {
    await AsyncStorage.setItem('firebase_token', token);
    //console.log('Token saved');
  } catch (e) {
    //console.log('Save error:', e);
  }
      // Send this token to your backend if needed
    } catch (e) {
      //console.log('[Splash] getFCMToken error:', e);
    }
  }

  const fadeIn = () => {
  setTimeout(() => {
    app_version_check();
  }, 1500);

  setTimeout(() => {
    getData();
  }, 3000);

  setTimeout(() => {
    safeNavigate('AuthStack');
  }, 8000);
};

  const app_version_check = async () => {
    try {
      let appVersion = DeviceInfo.getVersion();
      //console.log('[Splash] local app version:', appVersion);

      getApi(constants.app_version)
        .then(response => {
          //console.log('[Splash] app_version response:', response.data);
          if (response?.data?.status) {
            let version = response.data.data[0];
            let fetch_app_version =
              Platform.OS === 'ios' ? version.ios : version.android;

            //console.log( '[Splash] fetched app version from API:', fetch_app_version, );

            if (fetch_app_version != appVersion) {
              //console.log('[Splash] version mismatch, showing update alert');
              const message =
                Platform.OS === 'ios' ? messageList.t9 : messageList.t8;

              Alert.alert(messageList.t7, message, [
                {
                  text: 'UPDATE',
                  onPress: () => {
                    const link =
                      Platform.OS === 'ios'
                        ? 'https://apps.apple.com/in/app/star-link/id6446483968'
                        : 'https://play.google.com/store/apps/details?id=com.starlink';
                    Linking.openURL(link);
                    // After opening store, still continue app flow
                    app_registration_link_visible();
                  },
                },
                {
                  text: 'CONTINUE',
                  style: 'cancel',
                  onPress: () => {
                    //console.log( '[Splash] user chose CONTINUE without updating', );
                    app_registration_link_visible();
                  },
                },
              ]);
            } else {
              //console.log('[Splash] version matches, proceeding');
              app_registration_link_visible();
            }
          } else {
            //console.log( '[Splash] app_version status false, proceeding anyway', );
            app_registration_link_visible();
          }
        })
        .catch(err => {
          //console.log('[Splash] app_version_check API error:', err);
          app_registration_link_visible();
        });
    } catch (e) {
      //console.log('[Splash] app_version_check catch:', e);
      app_registration_link_visible();
    }
  };

  const app_registration_link_visible = async () => {
    //console.log('[Splash] calling app_registration_link_visible');
    getApi(
      constants.app_registration_link_visible +
        '?preferred_app_lang=' +
        selectedLanguageIfSafe(),
    )
      .then(response => {
        //console.log( '[Splash] app_registration_link_visible response:', response.data, );
        if (response?.data?.status && response.data.data?.[0]) {
          const visibleValue =
            response.data.data[0].app_registration_link_visible ?? '0';
          AsyncStorage.setItem('link_visible', visibleValue).catch(err => {
            //console.log('[Splash] AsyncStorage set link_visible error:', err);
          });
        }
        getData();
      })
      .catch(err => {
        //console.log( '[Splash] app_registration_link_visible API error:', err, );
        getData(); // still continue even on error
      });
  };

  const selectedLanguageIfSafe = () => {
    try {
      // your original helper
      const lang = require('../../helper/constants/LanguageSelect').default;
      return lang();
    } catch (e) {
      //console.log('[Splash] selectedLanguage error, defaulting English:', e);
      return 'English';
    }
  };

  const getData = async () => {
  try {
    const language_select = await AsyncStorage.getItem('language_select');
    DataStore.language = language_select || 'English';

    const value = await AsyncStorage.getItem('user_info');

    if (value) {
      const data = JSON.parse(value);

      dispatch(
        updateData({ role: data?.data?.role == 2 ? 'mason' : 'te' }),
      );

      safeNavigate('DrawerStack');
    } else {
      safeNavigate('AuthStack');
    }
  } catch (e) {
    safeNavigate('AuthStack');
  }
};

  return (
    <View style={{ height: '100%', width: '100%' }}>
      <Image
        style={{ height: '100%', width: '100%', resizeMode: 'stretch' }}
        source={ImagePath.SplashBackground}
      />
      <View
        style={{
          height: '100%',
          width: '100%',
          position: 'absolute',
          alignItems: 'center',
          justifyContent: 'center',
        }}>
        <Image
          style={{ height: '60%', width: '60%', resizeMode: 'contain' }}
          source={ImagePath.LogoAndTag}
        />
      </View>
    </View>
  );
};

export default Splash;
