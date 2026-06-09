/**
 * @format
 */
import messaging from '@react-native-firebase/messaging';
import 'react-native-gesture-handler'
import React from 'react'
import {AppRegistry} from 'react-native'
import App from './App'
import {name as appName} from './app.json'

import { Provider } from 'react-redux'
import store from './src/redux/store/store'

// AppRegistry.registerComponent(appName, () => App)

messaging().setBackgroundMessageHandler(async remoteMessage => {
  console.log('📦 Background message:', remoteMessage);
});

const RNRedux = () => (
    <Provider store={store}>
        <App />
    </Provider>
)

AppRegistry.registerComponent(appName, () => RNRedux)
