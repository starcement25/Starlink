import React, { useCallback, useRef, useState } from 'react'
import { Text, View, Image, TouchableOpacity, Platform } from 'react-native'
import styles from './NotificationStyle'
import { getApiWithHeader } from '../../../helper/http/Api'
import Loader from '../../../components/loader/Loader'
import Toast from 'react-native-toast-message'
import { FlashList } from '@shopify/flash-list'
import useTextValue from '../../../helper/constants/useTextValue'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

const Notification = (props) => {
    const textValue = useTextValue()

    const [loading, setLoading] = useState(false)
    const [notificationList, setNotificationList] = useState([])

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setLoading(true)
            getAllNotification(1, [])
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    const getAllNotification = (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        let url = `get-notifications?page=${page_value}&preferred_app_lang=` + selectedLanguage()
        getApiWithHeader(url)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
                    setLoading(false)
                    var a = value
                    a = [...a, ...response.data.data.notifications]
                    setNotificationList(a)
                    getAllNotification(page_value + 1, a)
                }
            })
            .catch(err => {
                setLoading(false)
            })
    }

    const renderItem = ({ item }) => (
        <View style={styles._lowerView._notification_section}>
            <Text style={styles._lowerView._notification_section._contact}>{convertForShowData(item?.msg)}</Text>
        </View>
    )

    const renderSeparator = ({ item, index }) => {
        return (
            <View style={{ width: '100%' }}>
                <View style={{ height: .5, width: '100%', backgroundColor: 'grey' }} />
            </View>
        )
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
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{convertForShowData(textValue.NOTIFICATION)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => props.navigation.goBack()}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>

                        {notificationList?.length == 0 ? <View style={styles._ndf}>
                            {selectedLanguage() == 'en' ? <Image style={styles._ndf._img} source={Icons.nodatafound_english} /> : null}
                            {selectedLanguage() == 'hi' ? <Image style={styles._ndf._img} source={Icons.nodatafound_hindi} /> : null}
                            {selectedLanguage() == 'as' ? <Image style={styles._ndf._img} source={Icons.nodatafound_assamese} /> : null}
                            {selectedLanguage() == 'bn' ? <Image style={styles._ndf._img} source={Icons.nodatafound_bengla} /> : null}
                        </View> : null}
                        <FlashList
                            data={notificationList}
                            renderItem={renderItem}
                            ItemSeparatorComponent={renderSeparator}
                        />
                    </View>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default Notification