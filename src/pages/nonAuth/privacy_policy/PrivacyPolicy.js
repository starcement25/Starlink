import React, { useEffect, useState } from 'react'
import { Image, Platform, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native'
import styles from './PrivacyPolicyStyle'
import { getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import HTMLView from 'react-native-htmlview'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import Icons from '../../../helper/image/ImageList'
const PrivacyPolicy = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [htmlContent, setHtmlContent] = useState('')

    useEffect(() => {
        const focusListener = props.navigation.addListener('focus', () => {
            setLoading(true)
            getData()
        })
        return focusListener
    }, [props.navigation])

    const getData = async () => {
        getApiWithHeader(constants.privacy_policy + '?preferred_app_lang' + selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    setHtmlContent('<body>' + response.data.data + '</body>')
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
                        <Text style={styles._upperView._txt}>{convertForShowData(textValue.PRIVACY_POLICY)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() =>{
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 10 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                        <ScrollView style={{ width: '90%', marginTop: '5%' }} showsVerticalScrollIndicato={false} showsHorizontalScrollIndicator={false}>
                            {htmlContent ? <HTMLView
                                value={htmlContent}
                                addLineBreaks={false}
                                stylesheet={richText}
                                style={richText}
                            /> : null}
                            <View style={{ height: 40, width: '100%' }}></View>
                        </ScrollView>
                    </View>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

const richText = StyleSheet.create({
    p: {
        marginTop: 0,
        marginBottom: -35,
        color: '#000000'
    }
})

export default PrivacyPolicy