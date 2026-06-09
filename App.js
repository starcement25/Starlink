import React, { useEffect } from 'react'

import { LogBox, View, Image, DeviceEventEmitter } from 'react-native'
import messaging from '@react-native-firebase/messaging';

LogBox.ignoreAllLogs()//Ignore all log notifications

//splahs
import Splash from './src/pages/splash/Splash'

//auth
import Welcome from './src/pages/auth/welcome/Welcome'
import Login from './src/pages/auth/login/Login'
import Otp from './src/pages/auth/otp/Otp'
import Verified from './src/pages/auth/verified/Verified'
import Registration from './src/pages/auth/registration/Registration'
import TeRegistration from './src/pages/auth/teRegistration/TeRegistration'

//nonAuth
import Dashboard from './src/pages/nonAuth/dashboard/Dashboard'
import MasonRegistration from './src/pages/nonAuth/masonRegistration/MasonRegistration'
import RegistrationSuccess from './src/pages/nonAuth/registrationSuccess/RegistrationSuccess'
import LiftingHistory from './src/pages/nonAuth/liftingHistory/LiftingHistory'
import RewardDetails from './src/pages/nonAuth/rewardDetails/RewardDetails'
import EditProfile from './src/pages/auth/editProfile/EditProfile'
import Notification from './src/pages/nonAuth/notification/Notification'
import GiftRedemption from './src/pages/nonAuth/giftRedemption/GiftRedemption'
import AddLefting from './src/pages/nonAuth/addLefting/AddLefting'
import Gift from './src/pages/nonAuth/gift/Gift'
import Order from './src/pages/nonAuth/order/Order'
import Support from './src/pages/nonAuth/support/Support'
import ViewSupport from './src/pages/nonAuth/viewSupport/ViewSupport'

import Aboutus from './src/pages/nonAuth/aboutus/Aboutus'
import Contactus from './src/pages/nonAuth/contactus/Contactus'
import Faq from './src/pages/nonAuth/faq/Faq'
import PrivacyPolicy from './src/pages/nonAuth/privacy_policy/PrivacyPolicy'
import TermCondition from './src/pages/nonAuth/term_condition/TermCondition'
import TeDealerLink from './src/pages/nonAuth/tedealerlink/TeDealerLink'

import Loader from './src/components/loader/Loader'
import Toast, { BaseToast, ErrorToast } from 'react-native-toast-message'

import { NavigationContainer } from '@react-navigation/native'
import { createNativeStackNavigator } from '@react-navigation/native-stack'
import { createDrawerNavigator } from '@react-navigation/drawer'

import Icon from 'react-native-vector-icons/FontAwesome'

import CustomDrawer from './src/components/customDrawer/CustomDrawer'

import { useDispatch, useSelector } from 'react-redux'
import WatchNetinfo from './src/components/watchNet/WatchNetinfo'

import DashboardTe from './src/pages/nonAuth/Dashboardte/DashboardTe'
import DashboardTeDetails from './src/pages/nonAuth/Dashboardte/DashboardTeDetails'

import LiftingStatus from './src/pages/nonAuth/liftingstatus/LiftingStatus'
import EditLiftingStatus from './src/pages/nonAuth/liftingstatus/EditLiftingStatus'
import AddLiftingTe from './src/pages/nonAuth/addLefting/AddLiftingTe'
import DealerRequest from './src/pages/nonAuth/requestdelaer/DealerRequest'
import useTextValue from './src/helper/constants/useTextValue'
import Icons from './src/helper/image/ImageList'
import Verifiedlanguage from './src/pages/auth/verified/Verifiedlanguage'
import ImagePath from './src/image/ImagePath'
import OrderEnquiryList from './src/pages/nonAuth/order_enquiry/OrderEnquiryList'
import NewEnquiryRequest from './src/pages/nonAuth/order_enquiry/NewEnquiryRequest'

import MassonList from './src/pages/nonAuth/aadhaarUpload/MassonList'
import UpdateMassonProfile from './src/pages/nonAuth/aadhaarUpload/UpdateMassonProfile'

const Stack = createNativeStackNavigator()
const Drawer = createDrawerNavigator()
let user

const toastConfig = {
  /*
    Overwrite 'success' type,
    by modifying the existing `BaseToast` component
  */
  success: (props) => (
    <BaseToast
      {...props}
      style={{ borderLeftColor: 'green' }}
      contentContainerStyle={{ paddingHorizontal: 15 }}
      text1Style={{
        fontSize: 13,
        fontWeight: '400'
      }}
      text2NumberOfLines={3}
    />
  ),
  error: (props) => (
    <ErrorToast
      {...props}
      text1Style={{
        fontSize: 13
      }}
      text2Style={{
        fontSize: 12
      }}
      text2NumberOfLines={3}
    />
  )
}

const AuthStack = () => (
  <Stack.Navigator initialRouteName='Login'>
    <Stack.Screen
      name='Welcome'
      component={Welcome}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='Login'
      component={Login}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='Registration'
      component={Registration}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='TeRegistration'
      component={TeRegistration}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='Otp'
      component={Otp}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='Verified'
      component={Verified}
      options={{ headerShown: false }}
    />
  </Stack.Navigator>
)

const NonAuthStack = () => (
  <Stack.Navigator initialRouteName='Dashboard'>
    <Stack.Screen
      name='Dashboard'
      component={Dashboard}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='MasonRegistration'
      component={MasonRegistration}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='RegistrationSuccess'
      component={RegistrationSuccess}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='LiftingHistory'
      component={LiftingHistory}
      options={{ headerShown: false }}
    />
    <Stack.Screen 
    name='Order'
    component={Order}
    options={{headerShown:false}}/>
    <Stack.Screen
      name='RewardDetails'
      component={RewardDetails}
      options={{ headerShown: false }}
    />

    <Stack.Screen
      name='GiftRedemption'
      component={GiftRedemption}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='AddLefting'
      component={AddLefting}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='Gift'
      component={Gift}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='Notification'
      component={Notification}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='Support'
      component={Support}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='ViewSupport'
      component={ViewSupport}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='DashboardTe'
      component={DashboardTe}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='DashboardTeDetails'
      component={DashboardTeDetails}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='LiftingStatus'
      component={LiftingStatus}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='EditLiftingStatus'
      component={EditLiftingStatus}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='AddLiftingTe'
      component={AddLiftingTe}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='DealerRequest'
      component={DealerRequest}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='TeDealerLink'
      component={TeDealerLink}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='OrderEnquiryList'
      component={OrderEnquiryList}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='NewEnquiryRequest'
      component={NewEnquiryRequest}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='MassonList'
      component={MassonList}
      options={{ headerShown: false }}
    />
    <Stack.Screen
      name='UpdateMassonProfile'
      component={UpdateMassonProfile}
      options={{ headerShown: false }}
    />
  </Stack.Navigator>
)

const DrawerStack = ({ navigation }) => (

  <Drawer.Navigator
    drawerContent={props => <CustomDrawer {...props} />}
    screenOptions={{
      headerShown: false,
      drawerLabelStyle: {
        marginLeft: -25,
        fontWeight: '400',
      },

      drawerActiveBackgroundColor: '#FFF0F1',
      drawerActiveTintColor: '#000',
      drawerInactiveBackgroundColor: '#0000',
      drawerInactiveTintColor: '#888',
      drawerStyle: {
        backgroundColor: '#0000',
      },
      drawerItemStyle: {
        marginVertical: 3,
        marginHorizontal: 15
      },
    }}

  >
    <Drawer.Screen
      name={useTextValue().Home}
      component={NonAuthStack}
      options={{
        drawerIcon: ({ color }) => <View style={{ backgroundColor: '#fff', padding: 8, borderRadius: 5 }}>
          <Image style={{ width: 16, height: 16, resizeMode: 'contain' }} source={ImagePath.LeftMenuHomeIcon} />
        </View>,
      }}
    />

    <Drawer.Screen
      name={useTextValue().Profile}
      component={EditProfile}
      options={{
        drawerIcon: ({ color }) => <View style={{ backgroundColor: '#fff', padding: 8, borderRadius: 5 }}>
          <Image style={{ width: 16, height: 16, resizeMode: 'contain' }} source={ImagePath.LeftMenuProfileIcon} />
        </View>,
      }}
    />

    <Drawer.Screen
      name={useTextValue().Order_List}
      component={Order}
      options={{
        drawerItemStyle: { display: user?.role == 'mason' ? 'flex' : 'none' },
        drawerIcon: ({ color }) => <View style={{ backgroundColor: '#fff', padding: 8, borderRadius: 5, marginLeft: 5 }}>
          <Image style={{ width: 16, height: 16, resizeMode: 'contain', tintColor: '#F8373C' }} source={ImagePath.LeftMenuOrderListIcon} />
        </View>,
      }}
    />

    <Drawer.Screen
      name={useTextValue().About_Us}
      component={Aboutus}
      options={{
        drawerIcon: ({ color }) => <View style={{ backgroundColor: '#fff', padding: 8, borderRadius: 5 }}>
          <Image style={{ width: 16, height: 16, resizeMode: 'contain' }} source={ImagePath.LeftMenuAboutUsIcon} />
        </View>,
      }}

    />

    <Drawer.Screen
      name={useTextValue().Contact_Us}
      component={Contactus}
      options={{
        drawerIcon: ({ color }) => <View style={{ backgroundColor: '#fff', padding: 8, borderRadius: 5 }}>
          <Image style={{ width: 16, height: 16, resizeMode: 'contain' }} source={ImagePath.LeftMenuContactUsIcon} />
        </View>,
      }}
    />

    <Drawer.Screen
      name={useTextValue().Terms_Conditions}
      component={TermCondition}
      options={{
        drawerIcon: ({ color }) => <View style={{ backgroundColor: '#fff', padding: 8, borderRadius: 5 }}>
          <Image style={{ width: 16, height: 16, resizeMode: 'contain' }} source={ImagePath.LeftMenuTermsConditionsIcon} />
        </View>,
      }}
    />

    <Drawer.Screen
      name={useTextValue().Privacy_Policy}
      component={PrivacyPolicy}
      options={{
        drawerIcon: ({ color }) => <View style={{ backgroundColor: '#fff', padding: 8, borderRadius: 5 }}>
          <Image style={{ width: 16, height: 16, resizeMode: 'contain' }} source={ImagePath.LeftMenuPrivacyPolicyIcon} />
        </View>,
      }}
    />

    {/* <Drawer.Screen
      name={'FAQ (GPA Insurance Policy)'}
      component={Faq}
      options={{
        drawerIcon: ({ color }) => <View style={{ backgroundColor: '#fff', padding: 8, borderRadius: 5 }}>
          <Image style={{ width: 16, height: 16, resizeMode: 'contain' }} source={ImagePath.LeftMenuFaqIcon} />
        </View>,
      }}
    /> */}
  </Drawer.Navigator>
)

const VerifiedStack = () => (
  <Stack.Navigator initialRouteName='Verifiedlanguage'>
    <Stack.Screen
      name='Verifiedlanguage'
      component={Verifiedlanguage}
      options={{ headerShown: false }}
    />
  </Stack.Navigator>
)

const App = () => {

  user = useSelector((state) => state.user)
  const dispatch = useDispatch()
  useEffect(() => {
    // Foreground message listener
    const unsubscribe = messaging().onMessage(async remoteMessage => {
      console.log('📩 Foreground notification:', remoteMessage);

      Alert.alert(
        remoteMessage.notification?.title || 'Notification',
        remoteMessage.notification?.body || ''
      );
    });

    return unsubscribe;
  }, []);

  //handle taps---
//   useEffect(() => {
//   // When user taps notification while app is in background
//   const unsubscribe = messaging().onNotificationOpenedApp(remoteMessage => {
//     console.log(
//       '📲 Opened from background:',
//       remoteMessage
//     );

//     // Example navigation
//     // navigation.navigate('SomeScreen', remoteMessage.data);
//   });

//   return unsubscribe;
// }, []);

useEffect(() => {
  messaging()
    .getInitialNotification()
    .then(remoteMessage => {
      if (remoteMessage) {
        console.log(
          '🚀 Opened from killed state:',
          remoteMessage
        );

        // Example navigation
        // navigation.navigate('SomeScreen', remoteMessage.data);
      }
    });
}, []);




  return (
    <>
      <NavigationContainer>
        <Stack.Navigator initialRouteName='Splash' screenOptions={{
          headerShown: false,
          gestureEnabled: false
        }}>
          <Stack.Screen
            name='Splash'
            component={Splash}
          />
          <Stack.Screen
            name='AuthStack'
            component={AuthStack}
          />
          <Stack.Screen
            name='DrawerStack'
            component={DrawerStack}
          />
          <Stack.Screen
            name='VerifiedStack'
            component={VerifiedStack}
          />
        </Stack.Navigator>
      </NavigationContainer>
      <Toast config={toastConfig} />
    </>
  )
}

export default App

//babel.config.js -> add plugins: ['react-native-reanimated/plugin'], after presets then hit yarn start --reset-cache