import React, { useState, useEffect } from 'react'
import { ScrollView, Text, TextInput, View, Image, TouchableOpacity } from 'react-native'
import Toast from 'react-native-toast-message'
import styles from './TeRegistrationStyle'
import MultiSelectExample from '../../../components/multiselect/MultiSelectDealerRssd'
import { getApi, postApi } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

var branch_ids = []

const TeRegistration = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [name, setName] = useState('')
    const [employeeCode, setEmployeeCode] = useState('')
    const [phone, setPhone] = useState('')
    const [address1, setAddress1] = useState('')
    const [address2, setAddress2] = useState('')
    const [city, setCity] = useState('')
    const [district, setDistrict] = useState('')
    const [state, setState] = useState('')
    const [pin, setPin] = useState('')
    const [branchList, setBranchList] = useState([])

    useEffect(() => {
        setLoading(true)
        get_all_branch()
    }, [])

    const getData = (data) => {
        branch_ids = data.data
    }

    const form_validation = () => {
        if (name == '') {
            showToast('error', messageList.t12)
        } else if (employeeCode == '') {
            showToast('error', messageList.t28)
        } else if (phone == '') {
            showToast('error', messageList.t13)
        } else if (address1 == '') {
            showToast('error', messageList.t18)
        } else if (city == '') {
            showToast('error', messageList.t19)
        } else if (district == '') {
            showToast('error', messageList.t20)
        } else if (state == '') {
            showToast('error', messageList.t21)
        } else if (pin == '') {
            showToast('error', messageList.t23)
        } else if (branch_ids.length == 0) {
            showToast('error', messageList.t29)
        } else {

            setLoading(true)
            var arr = [name, address1, address2, city, district, state, pin]
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

                    let obj = {
                        phone: phone,
                        name: result.data.translations[0].translatedText,
                        emp_code: employeeCode,
                        branch_ids: '[' + branch_ids.toString() + ']',
                        address1: result.data.translations[1].translatedText,
                        address2: result.data.translations[2].translatedText,
                        city: result.data.translations[3].translatedText,
                        district: result.data.translations[4].translatedText,
                        state: result.data.translations[5].translatedText,
                        pin: result.data.translations[6].translatedText,
                    }
                    register_service(obj)
                })
                .catch(err => {
                    let obj = {
                        phone: phone,
                        name: arr[0],
                        emp_code: employeeCode,
                        branch_ids: '[' + branch_ids.toString() + ']',
                        address1: arr[1],
                        address2: arr[2],
                        city: arr[3],
                        district: arr[4],
                        state: arr[5],
                        pin: arr[6],
                    }
                    register_service(obj)
                    // setLoading(false)
                    // Toast.show({
                    //     type: 'error',
                    //     text2: 'Sorry google translations not working',
                    //     text2NumberOfLines: 2
                    // })
                })

        }
    }

    const showToast = (type, msg) => {
        Toast.show({
            type: type,
            text2: msg,
            text2NumberOfLines: 2
        })
    }

    const get_all_branch = async () => {
        getApi(constants.test_all_branch + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    setBranchList(response.data.data)
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

    const register_service = async (obj) => {
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('phone', convertForUploadData(obj.phone))
        formData.append('name', convertForUploadData(obj.name))
        formData.append('emp_code', obj.emp_code)
        formData.append('branch_ids', obj.branch_ids)
        formData.append('address1', convertForUploadData(obj.address1))
        formData.append('address2', convertForUploadData(obj.address2))
        formData.append('city', convertForUploadData(obj.city))
        formData.append('district', convertForUploadData(obj.district))
        formData.append('state', convertForUploadData(obj.state))
        formData.append('pincode', convertForUploadData(obj.pin))
        formData.append('preferred_app_lang', selectedLanguage())
        postApi(constants.test_register_te, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    props.navigation.goBack()
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
                    <Text style={styles._upperView._txt}>{convertForShowData(textValue.TECHNICAL_ENGINEER)}</Text>
                    <Text style={styles._upperView._txt}>{convertForShowData(textValue.REGISTRATION)}</Text>
                    <TouchableOpacity style={styles._upperView._back} onPress={() => props.navigation.goBack()}>
                        <Image style={styles._upperView._back._img} source={Icons.back} />
                    </TouchableOpacity>
                </View>

                <View style={styles._lowerView}>

                    <ScrollView style={styles._lowerView._scrollView}>
                        <View style={styles._lowerView._scrollView._view}>
                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Full_name}
                                    onChangeText={text => {
                                        const filtered = text.replace(/[^A-Za-z\s]/g, '')
                                        setName(filtered)
                                    }}
                                    autoCapitalize="words"
                                    keyboardType="default"
                                    value={convertForShowData(name)} />
                            </View>

                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Employee_Code}
                                    style={styles._lowerView._scrollView._input._txt_input}
                                    onChangeText={text => setEmployeeCode(text)}
                                    value={convertForShowData(employeeCode)} />
                            </View>

                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <View style={{ flexDirection: 'row' }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'center', borderRightColor: '#a8a8a8', borderWidth: 1, borderTopColor: '#00000000', borderBottomColor: '#00000000', borderLeftColor: '#00000000' }}>
                                        <Text style={{ color: '#000' }}>+91</Text>
                                    </View>
                                    <TextInput
                                        placeholderTextColor='#a8a8a8'
                                        placeholder={textValue.Phone}
                                        style={[styles._lowerView._scrollView._input._txt_input, { marginLeft: 5 }]}
                                        onChangeText={text => setPhone(text)}
                                        value={convertForShowData(phone)}
                                        keyboardType='phone-pad' />
                                </View>
                            </View>

                            <View style={styles._lowerView._scrollView._input_area}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    multiline={true}
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Address_1}
                                    onChangeText={text => setAddress1(text)}
                                    value={convertForShowData(address1)} />
                            </View>

                            <View style={styles._lowerView._scrollView._input_area}>
                                <TextInput
                                    multiline={true}
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Address_2}
                                    onChangeText={text => setAddress2(text)}
                                    value={convertForShowData(address2)} />
                            </View>

                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.City}
                                    onChangeText={text => setCity(text)}
                                    value={convertForShowData(city)} />
                            </View>

                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.District}
                                    onChangeText={text => setDistrict(text)}
                                    value={convertForShowData(district)} />
                            </View>

                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.State}
                                    onChangeText={text => setState(text)}
                                    value={convertForShowData(state)} />
                            </View>

                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Pin}
                                    onChangeText={text => setPin(text)}
                                    value={convertForShowData(pin)}
                                    keyboardType='number-pad'
                                    maxLength={6} />
                            </View>

                            <View style={{ width: '100%', height: 15 }}></View>

                            <View style={{ width: '90%', marginBottom: -28, position: 'relative', height: 15 }}>
                                <Text style={{ color: '#ee1d23', position: 'absolute', right: -2 }}>*</Text>
                            </View>

                            <View style={[styles._multi_select, { height: 'auto', marginTop: 15 }]}>
                                <Text style={{ position: 'absolute', top: -4, left: 0, color: '#ee1d23' }}>*</Text>
                                <MultiSelectExample sendData={getData} data={{ items: branchList, title: textValue.Select_branch }} />
                            </View>

                            <TouchableOpacity onPress={() => { form_validation() }} style={styles._lowerView._btn}>
                                <Text style={styles._lowerView._btn._txt}>{convertForShowData(textValue.Register)}</Text>
                            </TouchableOpacity>
                        </View>
                    </ScrollView>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default TeRegistration