import React, { useEffect, useState } from 'react'
import { ScrollView, Text, TextInput, View, Image, TouchableOpacity, KeyboardAvoidingView } from 'react-native'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import moment from 'moment'
import Toast from 'react-native-toast-message'
import AsyncStorage from '@react-native-async-storage/async-storage'
import styles from './EditLiftingStyle'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import Constants from '../../../helper/constants/Constants'
import useTextValue from '../../../helper/constants/useTextValue'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const EditLiftingStatus = props => {
    const textValue = useTextValue()

    const [liftingProduct, setLiftingProduct] = useState('')
    const [isToDate, setIsToDate] = useState(false)
    const [lDate, setLDate] = useState('')
    const [masonName, setMasonName] = useState('')
    const [masonPhone, setMasonPhone] = useState('')
    const [masonBag, setMasonBag] = useState('')
    const [liftingId, setLiftingId] = useState('')

    useEffect(() => {
        let arr = props.route.params.data.dataItem
        for (let i = 0; i < arr.length; i++) {
            if (arr[i].key == 'lifting_id') {
                getApiForEdit(arr[i].value)
                setLiftingId(arr[i].value)
            }
        }
    }, [])

    const hideDatePickerTo = () => {
        setIsToDate(false)
    }

    const handleConfirmTo = date => {
        setLDate(moment(date).format('YYYY-MM-DD'))
        console.warn('A date has been picked: ', date)
        hideDatePickerTo()
    }

    const callEDitStatus = async () => {
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('lifting_id', liftingId)
        formData.append('qty', convertForUploadData(masonBag))
        formData.append('preferred_app_lang', selectedLanguage())
        postApiWithHeader(Constants.te_edit_lifting, formData)
            .then(response => {
                if (response.data.status) {
                    props.navigation.push('LiftingStatus')
                }
            })
            .catch(err => { })
    }

    const showToast = (type, title, msg) => {
        Toast.show({
            ype: type,
            text1: title,
            text2: msg,
            text2NumberOfLines: 2
        })
    }

    const getApiForEdit = (id) => {
        let url = `te/starsaathi/edit-lifting?lifting_id=${id}&preferred_app_lang=` + selectedLanguage()
        getApiWithHeader(url)
            .then(response => {
                if (response.data.status) {
                    setLiftingProduct(response.data.data.product)
                    setLDate(response.data.data.lifting_date)
                    setMasonName(response.data.data.mason_name)
                    setMasonPhone(response.data.data.mason_phone)
                    setMasonBag(String(response.data.data.no_of_bags))
                } else {
                    setLoading(false)
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
            <KeyboardAvoidingView style={styles.container}>
                <ScrollView>
                    <View style={styles._bgColor}>
                        <View style={styles._upperView}>
                            <TouchableOpacity style={styles._upperView._back_btn} onPress={() =>setTimeout(()=>{
                                props.navigation.goBack()
                            },500)}>
                                <Image style={styles._upperView._back_btn._img} source={Icons.back} />
                            </TouchableOpacity>
                            <View style={{ justifyContent: 'center', alignItems: 'center' }}>
                                <Text style={[styles._upperView._txt, { marginBottom: 20 }]}>
                                    {convertForShowData(textValue.Edit_Pending_Lifting)}
                                </Text>
                            </View>
                        </View>
                        <View style={styles._lowerView}>
                            <View style={{ paddingTop: 30, paddingHorizontal: 26 }}>
                                <View>
                                    <View>
                                        <Text>{convertForShowData(textValue.Product)} :</Text>
                                    </View>
                                    <View style={{ width: '100%', height: 49, backgroundColor: '#F5F5F5', borderRadius: 20, justifyContent: 'center', paddingLeft: 10, }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            value={convertForShowData(liftingProduct)}
                                            onChangeText={text => setLiftingProduct(text)}
                                            editable={false}
                                        />
                                    </View>
                                </View>
                                <View style={{ paddingTop: 15 }}>
                                    <View>
                                        <Text>{convertForShowData(textValue.Lifting_Date)} :</Text>
                                    </View>
                                    <View style={{ width: '100%', height: 49, backgroundColor: '#F5F5F5', borderRadius: 20, justifyContent: 'center', paddingLeft: 10, }}>
                                        <Text>{convertForShowData(lDate)}</Text>
                                    </View>
                                </View>
                                <View style={{ paddingTop: 15 }}>
                                    <View>
                                        <Text>{convertForShowData(textValue.Mason_Name)}:</Text>
                                    </View>
                                    <View style={{ width: '100%', height: 49, backgroundColor: '#F5F5F5', borderRadius: 20, justifyContent: 'center', paddingLeft: 10, }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            value={convertForShowData(masonName)}
                                            onChangeText={text => setMasonName(text)}
                                            editable={false}
                                        />
                                    </View>
                                </View>
                                <View style={{ paddingTop: 15 }}>
                                    <View>
                                        <Text>{convertForShowData(textValue.Mason_Phone)} :</Text>
                                    </View>
                                    <View style={{ width: '100%', height: 49, backgroundColor: '#F5F5F5', borderRadius: 20, justifyContent: 'center', paddingLeft: 10, }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            value={convertForShowData(masonPhone)}
                                            onChangeText={text => setMasonPhone(text)}
                                            editable={false}
                                        />
                                    </View>
                                </View>

                                <View style={{ paddingTop: 15 }}>
                                    <View>
                                        <Text>{convertForShowData(textValue.No_of_Bags)} :</Text>
                                    </View>
                                    <View style={{ width: '100%', height: 49, backgroundColor: '#F5F5F5', borderRadius: 20, justifyContent: 'center', paddingLeft: 10, }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            value={convertForShowData(masonBag)}
                                            onChangeText={text => setMasonBag(text)}
                                            keyboardType={'number-pad'}
                                        />
                                    </View>
                                </View>
                                <View style={{ width: '100%', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
                                    <TouchableOpacity onPress={() => callEDitStatus()} style={{ paddingTop: 20, width: '100%' }}>
                                        <View style={{ height: 65, backgroundColor: '#509F39', borderRadius: 30, justifyContent: 'center', paddingLeft: 10, alignItems: 'center', }}>
                                            <Text style={{ color: '#FFFFFF' }}>{convertForShowData(textValue.Accept)}</Text>
                                        </View>
                                    </TouchableOpacity>
                                </View>
                            </View>
                        </View>
                    </View>
                    <DateTimePickerModal
                        isVisible={isToDate}
                        mode='date'
                        onConfirm={handleConfirmTo}
                        onCancel={hideDatePickerTo}
                        date={new Date()}
                    />
                </ScrollView>
            </KeyboardAvoidingView>
        </SafeView>
    )
}
export default EditLiftingStatus
