import React, { useEffect, useState } from 'react'
import { Text, View, Image, TouchableOpacity, Platform } from 'react-native'
import { getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'


const GiftType = (props) => {
    const [userInfo, setUserInfo] = useState('')
    const [userId, setUserId] = useState('')
    const [loading, setLoading] = useState(false)

    useEffect(() => {
        my_profile()
    }, [])

    const my_profile = () => {
        setLoading(true)
        getApiWithHeader(constants.my_profile + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    setUserInfo(response.data)
                    setUserId(response?.data?.data?.id)
                }
            })
            .catch(err => { })
            .finally(() => {
                setLoading(false)
            })
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={{ width: '100%', height: '100%', flexDirection: 'column', backgroundColor: '#FFF' }}>
                <View style={{ width: '100%', height: 100, borderBottomLeftRadius: 25, borderBottomRightRadius: 25, backgroundColor: '#EE1D23' }} />
                <Image source={ImagePath.Design1} style={{ width: '50%', height: 120, resizeMode: 'contain' }} />
            </View>
            <View style={{ width: '100%', height: '100%', position: 'absolute', flexDirection: 'column' }}>
                <View style={{ height: Platform.OS == 'ios' ? 25 : 0 }} />
                <View style={{ width: '100%', height: 70 }}>
                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>GIFT TYPE</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                        <View style={{ position: 'absolute', borderTopLeftRadius: 20, borderTopRightRadius: 20, backgroundColor: '#FFF', height: 30, width: '100%' }}></View>
                        <View style={{ width: '100%', flexDirection: 'row', padding: 15, height: '100%' }}>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                onPress={() => {
                                    props.navigation.navigate('Vouchers', { userId: userId })
                                }}
                                style={{ flex: 1, height: 120, borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                                <Image source={ImagePath.GiftIcon} style={{ width: 45, height: 45 }} />
                                <View style={{ height: 5 }} />
                                <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>VOUCHERS</Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                onPress={() => {
                                    props.navigation.navigate('Gift', { obj: userInfo, user_type: userInfo.data.role == 2 ? 'mason' : 'te' })
                                }}
                                style={{ flex: 1, height: 120, borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                                <Image source={ImagePath.GiftIcon} style={{ width: 45, height: 45 }} />
                                <View style={{ height: 5 }} />
                                <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>PRODUCT</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </View>

            {loading ? <Loader /> : null}
        </SafeView>
    )
}
export default GiftType