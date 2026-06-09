import React, { useEffect, useState } from 'react'
import { SafeAreaView, ScrollView, Text, TextInput, View, Image, TouchableOpacity } from 'react-native'
import styles from './SupportStyle'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import Toast from 'react-native-toast-message'
import DropDownPicker from 'react-native-dropdown-picker'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const Support = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [msg, setMsg] = useState('')
    const [userId, setUserId] = useState('')
    const [open, setOpen] = useState(false)
    const [value, setValue] = useState('')
    const [reasonList, setReasonList] = useState([{ label: 'Not Delivered', value: 1 }, { label: 'Defective', value: 2 }])

    useEffect(() => {
        setLoading(true)
        my_profile()
    }, [])

    const my_profile = () => {
        getApiWithHeader(constants.my_profile+'?preferred_app_lang='+selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    setUserId(response?.data?.data?.id)
                } else {
                    showToast('error', response.data.msg)
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
            text2NumberOfLines:2
        })
    }

    const form_validation = () => {
        if (value == null) {
            showToast('error', messageList.t40)
        } else if (msg == '') {
            showToast('error', messageList.t35)
        } else {
            setLoading(true)
            changeLanguage()
        }
    }
    const changeLanguage=()=>{
        setLoading(true)
        var arr = [msg]
        const myHeaders = new Headers()
        myHeaders.append('Content-Type', 'application/json')

        const raw = JSON.stringify({
            'q': arr,
            'target': 'en'
        })

        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: raw,
            redirect: 'follow'
        }

        fetch('https://translation.googleapis.com/language/translate/v2?key=AIzaSyBGLsoao9R0m9mEYxVrvNWnSu2ullebn2I', requestOptions)
            .then((response) => response.json())
            .then((result) => {
                send_query(result.data.translations[0].translatedText)
            })
            .catch(err => {
                send_query(arr[0])
                // setLoading(false)
                //                 Toast.show({
                //                     type: 'error',
                //                     text2: 'Sorry google translations not working',
                //                     text2NumberOfLines: 2
                //                 })
            })
    }
    const send_query = async (comment) => {
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('user_id', userId)
        formData.append('order_id', props?.obj?.item?.order_id)
        formData.append('comment', convertForUploadData(comment))
        formData.append('support_type', convertForUploadData(value))
        formData.append('id', props?.obj?.item?.id)
        formData.append('preferred_app_lang', selectedLanguage())
        
        postApiWithHeader(constants.saveSupport, formData)
            .then(response => {
                setLoading(false)
                
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    props.sendData()
                } else {
                    showToast('error', response.data.msg)
                }
            })
            .catch(err => {
                
                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._bgColor}>
                <View style={styles._upperView}>
                    <Text style={styles._upperView._txt}>{convertForShowData(textValue.SUPPORT)}</Text>
                    <TouchableOpacity style={styles._upperView._back}
                        onPress={() => { props.sendData() }} >
                        <Image style={styles._upperView._back._img} source={Icons.back} />
                    </TouchableOpacity>
                </View>
                <View style={styles._lowerView}>
                    <ScrollView style={styles._scroll}>
                        <Text style={styles._get_in_touch}>{convertForShowData(textValue.Get_In_Touch)}</Text>
                        <View style={{ marginTop: 20, zIndex: 99, borderRightColor: open ? '#00000000' : '#a8a8a8', borderLeftColor: open ? '#00000000' : '#a8a8a8', borderTopColor: open ? '#00000000' : '#a8a8a8', borderBottomColor: open ? '#00000000' : '#a8a8a8', borderWidth: 1, height: open ? Platform.OS == 'android' ? 248 : 55 : 55, borderTopEndRadius: 25, borderTopStartRadius: 25, borderBottomStartRadius: open === true ? 5 : 25, borderBottomEndRadius: open === true ? 5 : 25 }}>
                            <DropDownPicker
                                listMode='SCROLLVIEW'
                                scrollViewProps={{ nestedScrollEnabled: true, }}
                                style={{
                                    backgroundColor: '#fff00000',
                                    borderColor: open ? '#a8a8a8' : '#00000000',
                                    borderRadius: 25,
                                    width: '100%',
                                }}
                                open={open}
                                value={value}
                                items={reasonList}
                                setOpen={setOpen}
                                setValue={setValue}
                                placeholder={textValue.Select_your_reason}
                                searchable={true}

                                onChangeValue={(value) => {
                                    if (value) { }
                                }}
                                textStyle={{ fontSize: 14, }}
                                dropDownContainerStyle={{
                                    borderWidth: 1,
                                    borderColor: '#a8a8a8',
                                    borderRadius: 25,
                                    zIndex: 99999999,
                                    elevation: 1000,
                                    width: '100%',
                                    backgroundColor: '#fff',
                                }}
                                searchPlaceholder={textValue.Search + '...'}
                                searchPlaceholderTextColor='#999'
                                searchContainerStyle={{ borderBottomColor: '#ddd', }}
                                searchTextInputStyle={{ color: '#000', borderColor: '#a8a8a8', }}
                            />
                        </View>
                        <View style={[styles._view._input, { height: 120, justifyContent: 'flex-start', padding: 10, marginTop: open ? Platform.OS == 'android' ? -175 : 20 : 20 }]}>
                            <TextInput
                                placeholderTextColor='#21211F'
                                placeholder={textValue.Message}
                                multiline={true}
                                onChangeText={text => setMsg(text)}
                                value={convertForShowData(msg)} />
                        </View>

                        <TouchableOpacity activeOpacity={0.8} onPress={() => { form_validation() }} style={styles._view._btn}>
                            <Text style={styles._view._btn._txt}>{convertForShowData(textValue.Submit)}</Text>
                        </TouchableOpacity>
                    </ScrollView>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default Support