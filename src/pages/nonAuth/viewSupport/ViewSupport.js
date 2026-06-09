import React, { useEffect, useState } from 'react'
import { Text, View, Image, TouchableOpacity } from 'react-native'
import Toast from 'react-native-toast-message'
import { ScrollView } from 'react-native-virtualized-view'
import styles from './ViewSupportStyle'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const ViewSupport = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [supportList, setSupportList] = useState([])

    useEffect(() => {
        setLoading(true)
        my_profile()
    }, [])

    const my_profile = () => {
        getApiWithHeader(constants.my_profile + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    getSupport(response?.data?.data?.id)
                } else {
                    setLoading(false)
                    showToast('error', response.data.msg)
                }
            })
            .catch(err => {
                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const getSupport = async (id) => {
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('user_id', id)
        formData.append('order_id', props?.obj?.item?.order_id)
        formData.append('id', props?.obj?.item?.id)
        formData.append('preferred_app_lang', selectedLanguage())

        postApiWithHeader(constants.getSupport, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    setSupportList(response.data.data)
                }
            })
            .catch(err => {
                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const showToast = (type, msg) => {
        Toast.show({ type: type, text2: msg, text2NumberOfLines: 2 })
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._bgColor}>
                <View style={styles._upperView}>
                    <Text style={styles._upperView._txt}>{convertForShowData(textValue.SUPPORT_LIST)}</Text>
                    <TouchableOpacity style={styles._upperView._back} onPress={() => { props.sendData() }}>
                        <Image style={styles._upperView._back._img} source={Icons.back} />
                    </TouchableOpacity>
                </View>
                <View style={styles._lowerView}>
                    <ScrollView style={{ width: '100%' }}>
                        <View style={{ alignItems: 'center', marginTop: '8%', marginBottom: '35%' }}>
                            {supportList?.map((item) =>
                                <View style={{ width: '90%', height: 'auto', margin: 10, borderRadius: 5, padding: 10, shadowColor: '#000', borderWidth: 1, borderColor: '#00000025', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.2, shadowRadius: 2, overflow: 'hidden' }}>
                                    <View style={{ flexDirection: 'row', marginTop: 5 }}>
                                        <Text style={{ fontSize: 12, fontWeight: '600' }}>{convertForShowData(textValue.Name)} : </Text>
                                        <Text style={{ fontSize: 10, fontWeight: '600', width: '80%' }}>{convertForShowData(item.mason_name)}</Text>
                                    </View>
                                    <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 5 }}>
                                        <Text style={{ fontSize: 12, fontWeight: '600' }}>{convertForShowData(textValue.Comment)} : </Text>
                                        <Text style={{ fontSize: 10, fontWeight: '600' }}>{convertForShowData(item.comment)}</Text>
                                    </View>
                                    <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 5 }}>
                                        <Text style={{ fontSize: 12, fontWeight: '600' }}>{convertForShowData(textValue.Status)} : </Text>
                                        <Text style={{ fontSize: 10, fontWeight: '600' }}>{item.status == 1 ? convertForShowData(textValue.Pending) : item.status == 2 ? convertForShowData(textValue.Resolved) : convertForShowData(textValue.Rejected)}</Text>
                                    </View>
                                </View>)}
                            {supportList?.length == 0 ? <Text>{convertForShowData(textValue.No_Data_Found)}</Text> : null}
                        </View>
                    </ScrollView>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default ViewSupport