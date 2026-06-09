import React, { useState, useRef, useCallback } from 'react'
import { ScrollView, Text, TextInput, View, Image, TouchableOpacity, Platform } from 'react-native'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import DropDownPicker from 'react-native-dropdown-picker'
import moment from 'moment'
import Toast from 'react-native-toast-message'
import styles from './RegistrationStyle'
import MultiSelectExample from '../../../components/multiselect/MultiSelectDealerRssd'
import { getApi, postApi } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import Camera from '../../../components/camera/Camera'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

var dealer_rssd = []
var dobType = ''

const Registration = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [isDatePickerVisible, setDatePickerVisibility] = useState(false)
    const [selectedDate, setSelectedDate] = useState('dd-mm-yyyy')
    const [open, setOpen] = useState(false)
    const [value, setValue] = useState('')
    const [branchDropdown, setBranchDropdown] = useState(false)
    const [branchDropdownValue, setBranchDropdownValue] = useState('')
    const [teDropdown, setTeDropdown] = useState(false)
    const [teDropdownValue, setTeDropdownValue] = useState('')
    const [teList, setTeList] = useState([])
    const [name, setName] = useState('')
    const [phone, setPhone] = useState('')
    const [address1, setAddress1] = useState('')
    const [address2, setAddress2] = useState('')
    const [city, setCity] = useState('')
    const [district, setDistrict] = useState('')
    const [state, setState] = useState('')
    const [countryName, setCountryName] = useState('India')
    const [pin, setPin] = useState('')
    const [aadhaar, setAadhaar] = useState('')
    const [materialStatus, setMaterialStatus] = useState([
        { label: 'Married', value: 1 },
        { label: 'Unmarried', value: 0 }
    ])
    const [branchList, setBranchList] = useState([])
    const [dealerRssdList, setDealerRssdList] = useState([])

    const [imgPickup, setImgPickup] = useState(false)
    const [image, setImage] = useState('')
    const [fileName, setFileName] = useState('')
    const [type, setType] = useState('')

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setLoading(true)
            get_branches(1, [])
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    const showDatePicker = (type) => {
        dobType = type
        setDatePickerVisibility(true)
    }

    const hideDatePicker = () => {
        setDatePickerVisibility(false)
    }

    const handleConfirm = (date) => {
        if (dobType == 'mason') {
            setSelectedDate(moment(date).format('DD/MM/yyyy'))
        }
        hideDatePicker()
    }

    const getData = (data) => {
        dealer_rssd = data.data
    }

    const form_validation = () => {
        if (name == '') {
            showToast('error', messageList.t12)
        } else if (phone == '') {
            showToast('error', messageList.t13)
        } else if (value == null) {
            showToast('error', messageList.t14)
        } else if (branchDropdownValue == null) {
            showToast('error', messageList.t15)
        } else if (dealer_rssd.length == 0) {
            showToast('error', messageList.t16)
        } else if (teDropdownValue == null) {
            showToast('error', messageList.t17)
        } else if (address1 == '') {
            showToast('error', messageList.t18)
        } else if (city == '') {
            showToast('error', messageList.t19)
        } else if (district == '') {
            showToast('error', messageList.t20)
        } else if (state == '') {
            showToast('error', messageList.t21)
        } else if (countryName == '') {
            showToast('error', messageList.t22)
        } else if (pin == '') {
            showToast('error', messageList.t23)
        } else if (selectedDate == 'dd-mm-yyyy') {
            showToast('error', messageList.t24)
        } else if (aadhaar == '') {
            showToast('error', messageList.t25)
        } else if (aadhaar.length != 12) {
            showToast('error', messageList.t26)
        } else if (fileName == '') {
            showToast('error', messageList.t27)
        } else {
            setLoading(true)
            var arr = [name, address1, address2, city, district, state, countryName, pin]
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
                        name: result.data.translations[0].translatedText,
                        phone: phone,
                        branch_id: branchDropdownValue,
                        address1: result.data.translations[1].translatedText,
                        address2: result.data.translations[2].translatedText,
                        city: result.data.translations[3].translatedText,
                        district: result.data.translations[4].translatedText,
                        state: result.data.translations[5].translatedText,
                        country: result.data.translations[6].translatedText,
                        pin: result.data.translations[7].translatedText,
                        dob: moment(selectedDate, 'DD-MM-YYYY').format('yyyy-MM-DD'),
                        aadhaar_no: aadhaar,
                        marital_status: value,
                        dealer_ids: '[' + dealer_rssd.toString() + ']',
                        te_id: teDropdownValue,
                        aadhaar_doc: {
                            uri: image,
                            name: fileName,
                            type: type,
                        }
                    }
                    register_service(obj)
                })
                .catch(err => {
                    let obj = {
                        name: arr[0],
                        phone: phone,
                        branch_id: branchDropdownValue,
                        address1: arr[1],
                        address2: arr[2],
                        city: arr[3],
                        district: arr[4],
                        state: arr[5],
                        country: arr[6],
                        pin: arr[7],
                        dob: moment(selectedDate, 'DD-MM-YYYY').format('yyyy-MM-DD'),
                        aadhaar_no: aadhaar,
                        marital_status: value,
                        dealer_ids: '[' + dealer_rssd.toString() + ']',
                        te_id: teDropdownValue,
                        aadhaar_doc: {
                            uri: image,
                            name: fileName,
                            type: type,
                        }
                    }
                    register_service(obj)
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

    const get_branches = async (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        getApi(constants.test_branch + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
                    let data = response.data.data
                    let branches = []
                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            label: data[i].name,
                            value: data[i].id
                        }
                        branches.push(obj)
                    }
                    var a = value
                    a = [...a, ...branches]
                    setBranchList(a)
                    if (page_value == 1) { get_dealer_rssd(1, []) }
                    get_branches(page_value + 1, a)
                } else {
                    setLoading(false)
                    if (page_value == 1) {
                        showToast('error', response.data.msg)
                    }
                }
            })
            .catch(err => { setLoading(false) })
    }

    const get_dealer_rssd = async (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        getApi(constants.test_dealer + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
                    var a = value
                    a = [...a, ...response.data.data]
                    setDealerRssdList(a)
                    if (page_value == 1) { get_te() }
                    get_dealer_rssd(page_value + 1, a)
                } else {
                    setLoading(false)
                    if (page_value == 1) { showToast('error', response.data.msg) }
                }
            })
            .catch(err => { setLoading(false) })
    }

    const get_te = async () => {
        getApi(constants.test_te + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    let data = response.data.data
                    let te_list = []
                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            label: data[i].name,
                            value: data[i].id
                        }
                        te_list.push(obj)
                    }
                    setTeList(te_list)
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

    function getImage(data) {
        setImgPickup(!imgPickup)
        if (data != null) {
            try {
                setImage(data.res.assets[0].uri)
                setFileName(data.res.assets[0].fileName)
                setType(data.res.assets[0].type)
            }
            catch (err) { }
        }
    }

    const register_service = async (obj) => {
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('name', obj.name)
        formData.append('phone', convertForUploadData(obj.phone))
        formData.append('branch_id', obj.branch_id)
        formData.append('address1', convertForUploadData(obj.address1))
        formData.append('address2', convertForUploadData(obj.address2))
        formData.append('city', convertForUploadData(obj.city))
        formData.append('district', convertForUploadData(obj.district))
        formData.append('state', convertForUploadData(obj.state))
        formData.append('country', convertForUploadData(obj.country))
        formData.append('pincode', convertForUploadData(obj.pin))
        formData.append('dob', convertForUploadData(obj.dob))
        formData.append('aadhaar_no', convertForUploadData(obj.aadhaar_no))
        formData.append('aadhaar_doc', convertForUploadData(obj.aadhaar_doc))
        formData.append('marital_status', convertForUploadData(obj.marital_status))
        formData.append('dealer_ids', obj.dealer_ids)
        formData.append('te_id', obj.te_id)
        formData.append('preferred_app_lang', selectedLanguage())

        postApi(constants.test_register_mason, formData)
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
                    <Text style={styles._upperView._txt}>{convertForShowData(textValue.MASON)}</Text>
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
                                    placeholder={textValue.Full_name_of_mason}
                                    onChangeText={text => setName(text)}
                                    value={convertForShowData(name)} />
                            </View>
                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <View style={{ flexDirection: 'row' }}>
                                    <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'center', borderRightColor: '#a8a8a8', borderWidth: 1, borderTopColor: '#00000000', borderBottomColor: '#00000000', borderLeftColor: '#00000000' }}>
                                        <Text style={{ color: '#000' }}>+91</Text>
                                    </View>
                                    <TextInput placeholderTextColor='#a8a8a8' placeholder={textValue.Enter_Mobile_Number} style={[styles._lowerView._scrollView._input._txt_input, { marginLeft: 5 }]} onChangeText={text => setPhone(text)} value={convertForShowData(phone)} keyboardType='phone-pad' />
                                </View>
                            </View>
                            <View style={{ zIndex: 999, marginTop: 20, borderRightColor: '#a8a8a8', borderLeftColor: '#a8a8a8', borderTopColor: '#a8a8a8', borderBottomColor: open === true ? '#00000000' : '#a8a8a8', borderWidth: 1, height: open === true ? 'auto' : 55, borderTopEndRadius: 25, borderTopStartRadius: 25, borderBottomStartRadius: open === true ? 5 : 25, borderBottomEndRadius: open === true ? 5 : 25 }}>
                                <DropDownPicker
                                    style={{ backgroundColor: '#fff00000', borderColor: '#a8a8a800', borderRadius: 25, width: '90%', marginTop: 0 }}
                                    open={open}
                                    value={value}
                                    items={materialStatus}
                                    setOpen={setOpen}
                                    setValue={setValue}
                                    placeholder={textValue.Marital_Status}
                                    textStyle={{ fontSize: 14, }}
                                    dropDownContainerStyle={{ borderWidth: 1, borderColor: '#a8a8a8', zIndex: 99999999, elevation: 1000, width: '90%', backgroundColor: '#fff' }}
                                    searchPlaceholder={textValue.Search + '...'}
                                    searchPlaceholderTextColor='#999'
                                    searchContainerStyle={{ borderBottomColor: '#ddd', }}
                                    searchTextInputStyle={{ color: '#000', borderColor: '#a8a8a8', }} />
                            </View>
                            <View style={{ width: '100%', height: 15 }}></View>
                            <View style={{ width: '90%', marginBottom: -33, position: 'relative', height: 15 }}>
                                <Text style={{ color: '#ee1d23', position: 'absolute', right: -2 }}>*</Text>
                            </View>
                            <View style={{ zIndex: branchDropdown ? 9999 : 99, marginTop: 20, borderRightColor: branchDropdown ? '#00000000' : '#a8a8a8', borderLeftColor: branchDropdown ? '#00000000' : '#a8a8a8', borderTopColor: branchDropdown ? '#00000000' : '#a8a8a8', borderBottomColor: branchDropdown ? '#00000000' : '#a8a8a8', borderWidth: 1, height: branchDropdown ? Platform.OS == 'android' ? 250 : 'auto' : 55, borderTopEndRadius: 25, borderTopStartRadius: 25, borderBottomStartRadius: branchDropdown === true ? 5 : 25, borderBottomEndRadius: branchDropdown === true ? 5 : 25 }}>
                                <DropDownPicker
                                    style={{ backgroundColor: '#fff', borderColor: branchDropdown ? '#a8a8a8' : '#00000000', borderRadius: 25, width: '90%', marginTop: 0 }}
                                    open={branchDropdown}
                                    value={branchDropdownValue}
                                    items={branchList}
                                    setOpen={setBranchDropdown}
                                    setValue={setBranchDropdownValue}
                                    placeholder={textValue.Select_branch}
                                    textStyle={{ fontSize: 14, }}
                                    dropDownContainerStyle={{ borderWidth: 1, borderColor: '#a8a8a8', zIndex: 99999999, elevation: 1000, width: '90%', backgroundColor: '#fff' }}
                                    searchPlaceholder={textValue.Search + '...'}
                                    searchPlaceholderTextColor='#999'
                                    searchContainerStyle={{ borderBottomColor: '#ddd', }}
                                    searchTextInputStyle={{ color: '#000', borderColor: '#a8a8a8', }} />
                            </View>
                            <View style={{ width: '100%', height: 15 }}></View>
                            <View style={{ width: '90%', marginBottom: -28, position: 'relative', height: 15 }}>
                                <Text style={{ color: '#ee1d23', position: 'absolute', right: -2 }}>*</Text>
                            </View>
                            <View style={[styles._multi_select, { height: 'auto', marginTop: Platform.OS == 'android' ? branchDropdown ? -180 : 15 : 15 }]}>
                                <Text style={{ position: 'absolute', top: -4, left: 0, color: '#ee1d23' }}>*</Text>
                                <MultiSelectExample sendData={getData} data={{ items: dealerRssdList, title: 'Dealer/RSSD/Sub-Dealer' }} />
                            </View>
                            <View style={{ width: '90%', marginBottom: -18, position: 'relative', height: 15 }}>
                                <Text style={{ color: '#ee1d23', position: 'absolute', right: -2 }}>*</Text>
                            </View>
                            <View style={{ zIndex: teDropdown ? 9999 : 99, marginTop: 20, borderRightColor: teDropdown ? '#00000000' : '#a8a8a8', borderLeftColor: teDropdown ? '#00000000' : '#a8a8a8', borderTopColor: teDropdown ? '#00000000' : '#a8a8a8', borderBottomColor: teDropdown ? '#00000000' : '#a8a8a8', borderWidth: 1, height: teDropdown ? Platform.OS == 'android' ? 250 : 'auto' : 55, borderTopEndRadius: 25, borderTopStartRadius: 25, borderBottomStartRadius: teDropdown === true ? 5 : 25, borderBottomEndRadius: teDropdown === true ? 5 : 25 }}>
                                <DropDownPicker
                                    style={{ backgroundColor: '#fff', borderColor: teDropdown ? '#a8a8a8' : '#00000000', borderRadius: 25, width: '90%', marginTop: 0 }}
                                    open={teDropdown}
                                    value={teDropdownValue}
                                    items={teList}
                                    setOpen={setTeDropdown}
                                    setValue={setTeDropdownValue}
                                    placeholder={textValue.Select_Technical_Engineer}
                                    textStyle={{ fontSize: 14, }}
                                    dropDownContainerStyle={{ borderWidth: 1, borderColor: '#a8a8a8', zIndex: 99999999, elevation: 1000, width: '90%', backgroundColor: '#fff' }}
                                    searchPlaceholder={textValue.Search + '...'}
                                    searchPlaceholderTextColor='#999'
                                    searchContainerStyle={{ borderBottomColor: '#ddd', }}
                                    searchTextInputStyle={{ color: '#000', borderColor: '#a8a8a8', }} />
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
                                    placeholder={textValue.Country}
                                    onChangeText={text => setCountryName(text)}
                                    value={convertForShowData(countryName)} />
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
                            <Text style={{ fontSize: 14, fontWeight: '600', marginTop: 15 }}>{convertForShowData(textValue.DATE_OF_BIRTH)}</Text>
                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <Text style={styles._lowerView._scrollView._input._txt_input}>{convertForShowData(selectedDate)}</Text>
                                <TouchableOpacity onPress={() => { showDatePicker('mason') }} style={styles._lowerView._scrollView._dob}>
                                    <Image style={styles._lowerView._scrollView._dob._img} source={Icons.calender} />
                                </TouchableOpacity>
                            </View>
                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <TextInput
                                    placeholderTextColor='#a8a8a8'
                                    placeholder={textValue.Enter_Aadhar_number}
                                    onChangeText={text => setAadhaar(text)}
                                    value={convertForShowData(aadhaar)}
                                    maxLength={12}
                                    keyboardType='number-pad' />
                            </View>
                            <View style={styles._lowerView._scrollView._input}>
                                <Text style={{ position: 'absolute', top: -4, right: -2, color: '#ee1d23' }}>*</Text>
                                <Text style={{ width: '65%', color: '#a8a8a8' }}>{fileName == '' ? convertForShowData(textValue.Upload_Aadhaar) : convertForShowData(fileName)}</Text>
                                <TouchableOpacity onPress={() => { setImgPickup(true) }} style={[styles._lowerView._scrollView._btn, { width: 100 }]}>
                                    <Text style={styles._lowerView._scrollView._btn._txt}>{convertForShowData(textValue.Choose_File)}</Text>
                                </TouchableOpacity>
                            </View>
                            <TouchableOpacity onPress={() => { form_validation() }} style={styles._lowerView._btn}>
                                <Text style={styles._lowerView._btn._txt}>{convertForShowData(textValue.Register)}</Text>
                            </TouchableOpacity>
                        </View>
                    </ScrollView>
                </View>
            </View>

            <DateTimePickerModal
                isVisible={isDatePickerVisible}
                mode='date'
                onConfirm={handleConfirm}
                onCancel={hideDatePicker}
                maximumDate={new Date()}
                minimumDate={new Date(1900, 0, 1)}
                date={new Date()}
            />

            {loading ? <Loader /> : null}
            {imgPickup ? <Camera sendData={getImage} /> : null}

        </SafeView>
    )
}

export default Registration