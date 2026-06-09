import React, { useRef, useState, useEffect, useCallback } from 'react'
import { ScrollView, Text, TextInput, View, Image, TouchableOpacity, Keyboard, FlatList, ActivityIndicator, BackHandler, Platform, Dimensions, ToastAndroid } from 'react-native'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import moment from 'moment'
import Toast from 'react-native-toast-message'
import AsyncStorage from '@react-native-async-storage/async-storage'
import styles from './MasonRegistrationStyle'
import { postApi, postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import Camera from '../../../components/camera/Camera'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import DataStore from '../../../helper/constants/DataStore'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

var dobType = ''

const MasonRegistration = (props) => {
    const [isKeyboardVisible, setKeyboardVisible] = useState(false)
    const [keyboardHeight, setKeyboardHeight] = useState(0)
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [isDatePickerVisible, setDatePickerVisibility] = useState(false)
    const [selectedDate, setSelectedDate] = useState('dd-mm-yyyy')
    const [valueMarage, setValueMarage] = useState(99)
    const [label, setLabel] = useState('')
    const [branchDropdownValue, setBranchDropdownValue] = useState('')
    const [branchDropdownLabel, setBranchDropdownLabel] = useState('')
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
    const [voter, setVoter] = useState('')
    const [verifyText, setVerifyText] = useState(textValue.Send_OTP)
    const [materialStatus, setMaterialStatus] = useState()
    const [branchList, setBranchList] = useState([])
    const [dealerRssdList, setDealerRssdList] = useState([])
    const [searchDealerRssdList, setSearchDealerRssdList] = useState([])
    const [selectedDealerRssdList, setSelectedDealerRssdList] = useState([])
    const [selectedDealerRssdListTitle, setSelectedDealerRssdListTitle] = useState('')
    const [imgPickup, setImgPickup] = useState(false)
    const [filePickFor, setFilePickFor] = useState('')
    const [image, setImage] = useState('')
    const [fileName, setFileName] = useState('')
    const [type, setType] = useState('')
    const [voterImage, setVoterImage] = useState('')
    const [voterFileName, setVoterFileName] = useState('')
    const [voterType, setVoterType] = useState('')
    const [phoneVerifiedAt, setPhoneVerifiedAt] = useState('')
    const [spouse, setSpouse] = useState('')
    const [spouseDob, setSpouseDob] = useState('dd-mm-yyyy')
    const [flatlistLoader, setFlatlistLoader] = useState(false)
    const [dealerRssdListPopup, setDealerRssdListPopup] = useState(false)
    const [pickerPopup, setPickerPopup] = useState(false)
    const [dataSetList, setDataSetList] = useState([])
    const [typePopup, setTypePopup] = useState(0)
    const [searchText, setSearchText] = useState('')
    const [isVoterRequire, setIsVoterRequire] = useState(false)
    const [loading, setLoading] = useState(false)
    const [otp, setOtp] = useState(['', '', '', ''])
    const inputRefs = []

    const [isAadhaarUpload, setIsAadhaarUpload] = useState(false)
    const [isVoterUpload, setIsVoterUpload] = useState(false)

    const isFocusRef = useRef(false)

    const [isCallApi, setIsCallApi] = useState(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setLoading(true)
            setSelectedDealerRssdListTitle('')
            get_branches(1, [])
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    useEffect(() => {
        if (otp.every((digit) => digit !== '')) {
            Keyboard.dismiss()
        }
    }, [otp])

    useEffect(() => {
        switch (DataStore.language) {
            case 'Hindi':
                setMaterialStatus([
                    { label: 'विवाहित', value: 1 },
                    { label: 'अविवाहित', value: 0 }
                ])

                break
            case 'Assamese':
                setMaterialStatus([
                    { label: 'বিবাহিত', value: 1 },
                    { label: 'অবিবাহিত', value: 0 }
                ])
                break
            case 'Bengali':
                setMaterialStatus([
                    { label: 'বিবাহিত', value: 1 },
                    { label: 'অবিবাহিত', value: 0 }
                ])
                break
            default:
                setMaterialStatus([
                    { label: 'Married', value: 1 },
                    { label: 'Unmarried', value: 0 }
                ])
        }
    }, [])

    useEffect(() => {
        const showSubscription = Keyboard.addListener('keyboardDidShow', (event) => {
            setKeyboardHeight(event.endCoordinates.height)
            setKeyboardVisible(true)
        })

        const hideSubscription = Keyboard.addListener('keyboardDidHide', () => {
            setKeyboardHeight(0)
            setKeyboardVisible(false)
        })

        return () => {
            showSubscription.remove()
            hideSubscription.remove()
        }
    }, [])

    useEffect(() => {
        const backAction = () => {
            if (dealerRssdListPopup) {
                setDealerRssdListPopup(false)
                return true
            }
            return false
        }
        const backHandler = BackHandler.addEventListener(
            'hardwareBackPress',
            backAction
        )
        return () => backHandler.remove()
    }, [dealerRssdListPopup])

    useEffect(() => {
        if (typePopup == 2) {
            setDataSetList(branchList)
        }
    }, [branchList])

    useEffect(() => {
        if (isCallApi) {
            mason_register_service()
        }
    }, [isCallApi])

    const handleInputChange = (text, index) => {
        const newOtp = [...otp]
        newOtp[index] = text
        setOtp(newOtp)
        if (text && index < 3) {
            inputRefs[index + 1].focus()
        }
    }

    const handleKeyPress = (e, index) => {
        if (e.nativeEvent.key === 'Backspace' && index > 0 && !otp[index]) {
            inputRefs[index - 1].focus()
        }
    }

    const searchDealer = (text) => {
        const results = dealerRssdList.filter((item) =>
            item.keyword.toLowerCase().includes(text.toLowerCase())
        )
        setSearchText(text)
        setSearchDealerRssdList(results)
    }

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
        } else {
            setSpouseDob(moment(date).format('DD/MM/yyyy'))
        }
        hideDatePicker()
    }

    const form_validation = () => {
        if (name == '') {
            showToast('error', messageList.t12)
        } else if (phone == '') {
            showToast('error', messageList.t13)
        } else if (valueMarage == 99) {
            showToast('error', messageList.t14)
        } else if (branchDropdownValue == '') {
            showToast('error', messageList.t15)
        } else if (selectedDealerRssdList.length == 0) {
            showToast('error', messageList.t16)
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
        } else {

            var check = 1

            if (isVoterRequire) {

                if (aadhaar != '' || fileName != '') {
                    if (aadhaar == '') {
                        showToast('error', messageList.t25)
                        check = 0
                    } else if (aadhaar.length != 12) {
                        showToast('error', messageList.t26)
                        check = 0
                    } else if (!/^\d+$/.test(aadhaar)) {
                        showToast('error', messageList.t52)
                        check = 0
                    } else if (fileName == '') {
                        showToast('error', messageList.t27)
                        check = 0
                    } else {
                        setIsAadhaarUpload(true)
                    }
                }

                if (voter != '' || voterFileName != '') {
                    if (isVoterRequire && voter == '' && check == 1) {
                        showToast('error', messageList.t49)
                        check = 0
                    } else if (isVoterRequire && voter.length != 10 && check == 1) {
                        showToast('error', messageList.t50)
                        check = 0
                    } else if (isVoterRequire && !/^[a-zA-Z0-9]+$/.test(voter) && check == 1) {
                        showToast('error', messageList.t53)
                        check = 0
                    } else if (isVoterRequire && voterFileName == '' && check == 1) {
                        showToast('error', messageList.t51)
                        check = 0
                    } else {
                        setIsVoterUpload(true)
                    }
                }


                if (aadhaar == '' && fileName == '' && voter == '' && voterFileName == '') {
                    showToast('error', messageList.t54)
                    check = 0
                }

            } else {
                if (aadhaar == '') {
                    showToast('error', messageList.t25)
                    check = 0
                } else if (aadhaar.length != 12) {
                    showToast('error', messageList.t26)
                    check = 0
                } else if (!/^\d+$/.test(aadhaar)) {
                    showToast('error', messageList.t52)
                    check = 0
                } else if (fileName == '') {
                    showToast('error', messageList.t27)
                    check = 0
                } else {
                    setIsAadhaarUpload(true)
                }
            }


            if (check == 1) {
                setLoading(true)
                setIsCallApi(true)
            }

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
        getApiWithHeader(constants.get_branches + '?page=' + page_value + '&preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }

                setLoading(false)

                if (response?.data?.status) {
                    //console.log(response.data.data)

                    let data = response.data.data
                    let branches = []
                    for (var i = 0; i < data.length; i++) {
                        let obj = {
                            label: data[i].name,
                            value: data[i].id,
                            is_voter_require: data[i].is_voter_require
                        }
                        branches.push(obj)
                    }
                    var a = value
                    a = [...a, ...branches]
                    setBranchList(a)
                    get_branches(page_value + 1, a)
                }
                else {
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else {
                        if (page_value == 1) { showToast('error', response?.data?.msg) }
                    }
                }
            })
            .catch(err => {
                setLoading(false)
            })
    }

    const get_branch_user = async (id, page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('branch_id', id)
        formData.append('preferred_app_lang', selectedLanguage())
        postApi(constants.get_dealer_rssd + '?page=' + page_value, formData)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
                    var a = value
                    for (var i = 0; i < response.data.data.length; i++) {
                        var obj = response.data.data[i]
                        var isSelected = false
                        obj = { ...obj, isSelected }
                        a.push(obj)
                    }
                    var b = searchDealerRssdList
                    for (var i = 0; i < response.data.data.length; i++) {
                        var obj = response.data.data[i]
                        var isSelected = false
                        obj = { ...obj, isSelected }
                        b.push(obj)
                    }
                    const results = b.filter((item) =>
                        item.keyword.toLowerCase().includes(searchText)
                    )
                    setDealerRssdList(a)
                    setSearchDealerRssdList(results)
                    setFlatlistLoader(true)
                    setLoading(false)
                    get_branch_user(id, page_value + 1, a)
                } else {
                    if (page_value == 1) { showToast('error', response.data.msg) }
                    setLoading(false)
                    setFlatlistLoader(false)
                }
            })
            .catch(err => {
                setLoading(false)
                setFlatlistLoader(false)
            })
    }

    const send_otp = async () => {
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('phone', convertForUploadData(phone))
        formData.append('otp_purpose', 'mason registration')
        formData.append('preferred_app_lang', selectedLanguage())
        postApi(constants.send_otp_to_new_number, formData)
            .then(response => {
                //console.log(response.data)
                setLoading(false)
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    setVerifyText(textValue.Verify)
                } else {
                    showToast('error', messageList.error, response.data.msg)
                }
            })
            .catch(err => {
                setLoading(false)
                showToast('error', messageList.t4)
            })
    }

    const verify_otp = async () => {
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('phone', convertForUploadData(phone))
        formData.append('otp', convertForUploadData(otp.join('')))
        formData.append('preferred_app_lang', selectedLanguage())
        postApi(constants.verify_phone, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    setPhoneVerifiedAt(response.data.data.phone_verified_at)
                    setVerifyText(textValue.Verified)
                } else {
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
                if (filePickFor == 'aadhaar') {
                    setImage(data.res.assets[0].uri)
                    setFileName(data.res.assets[0].fileName)
                    setType(data.res.assets[0].type)
                }
                if (filePickFor == 'voter') {
                    setVoterImage(data.res.assets[0].uri)
                    setVoterFileName(data.res.assets[0].fileName)
                    setVoterType(data.res.assets[0].type)
                }

            } catch (err) { }
        }
    }

    const mason_register_service = async () => {
        if (!isCallApi) { return }
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('name', convertForUploadData(name))
        formData.append('phone', convertForUploadData(phone))
        formData.append('branch_id', branchDropdownValue.toString())
        formData.append('address1', convertForUploadData(address1))
        formData.append('address2', convertForUploadData(address2))
        formData.append('city', convertForUploadData(city))
        formData.append('district', convertForUploadData(district))
        formData.append('state', convertForUploadData(state))
        formData.append('country', convertForUploadData(countryName))
        formData.append('pincode', convertForUploadData(pin))
        formData.append('dob', convertForUploadData(moment(selectedDate, 'DD-MM-YYYY').format('yyyy-MM-DD')))

        formData.append('marital_status', valueMarage.toString())
        formData.append('phone_verified_at', convertForUploadData(phoneVerifiedAt))
        formData.append('dealer_ids', '[' + selectedDealerRssdList + ']')

        formData.append('spouse_name', convertForUploadData(spouse))
        formData.append('spouse_dob', convertForUploadData(moment(spouseDob, 'DD-MM-YYYY').format('yyyy-MM-DD')))
        formData.append('preferred_app_lang', selectedLanguage())



        if (isVoterUpload) {
            formData.append('voter_number', convertForUploadData(voter))
            formData.append('voter_doc', {
                uri: voterImage,
                name: voterFileName,
                type: voterType,
            })
            //console.log('voter data : ' + voterImage + ' // ' + voterFileName + ' // ' + voterType)

        }

        if (isAadhaarUpload) {
            formData.append('aadhaar_no', convertForUploadData(aadhaar))
            formData.append('aadhaar_doc', {
                uri: image,
                name: fileName,
                type: type,
            })
            //console.log('aadhaar data : ' + image + ' // ' + fileName + ' // ' + type)
        }

        //console.log(formData)

        postApiWithHeader(constants.mason_register, formData)
            .then(response => {
                //console.log(response)
                setIsCallApi(false)
                setLoading(false)
                if (response.data.status) {
                    showToast('success', response.data.msg)
                    props.navigation.goBack()
                } else {
                    showToast('error', response.data.msg)
                }
            })
            .catch(err => {
                //console.log(err)
                setIsCallApi(false)

                setLoading(false)
                showToast('error', messageList.t4)
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

    const setDataAndClosePopup = () => {
        setSearchText('')
        const selectedDealers = dealerRssdList.filter(item => item.isSelected).map(item => item.id)
        setSelectedDealerRssdList(selectedDealers)
        const selectedDealersName = dealerRssdList.filter(item => item.isSelected).map(item => item.name).join(',')

        setSelectedDealerRssdListTitle(selectedDealersName)

        setDealerRssdListPopup(false)
    }

    const changeStatus = (data) => {
        if (!flatlistLoader) {
            setDealerRssdList((dealerRssdList) =>
                dealerRssdList.map(item => {
                    if (data?.id !== undefined) {
                        return {
                            ...item,
                            isSelected: item.id === data.id ? !item.isSelected : item.isSelected,
                        }
                    }
                    return item
                })
            )
            setSearchDealerRssdList((searchDealerRssdList) =>
                searchDealerRssdList.map(item => {
                    if (data?.id !== undefined) {
                        return {
                            ...item,
                            isSelected: item.id === data.id ? !item.isSelected : item.isSelected,
                        }
                    }
                    return item
                })
            )
        }
    }

    const selectPopupValue = (data) => {
        if (typePopup == 1) {
            setValueMarage(data.value)
            setLabel(data.label)
            setPickerPopup(false)
        }

        if (typePopup == 2) {
            setBranchDropdownValue(data.value)
            setBranchDropdownLabel(data.label)
            setIsVoterRequire(data.is_voter_require)
            setDealerRssdList([])
            setSearchDealerRssdList([])
            setPickerPopup(false)
            get_branch_user(data.value, 1, [])
        }
    }

    const renderItem = ({ item }) => {
        return (
            <TouchableOpacity onPress={() => { changeStatus(item) }}>
                <View style={{ width: '100%', height: 40, paddingHorizontal: 10, alignItems: 'center', justifyContent: 'center', flexDirection: 'row', backgroundColor: !item?.isSelected ? '#fff' : '#EE1D23', marginVertical: 5, borderRadius: 5 }}>
                    <Text style={{ flex: 1, color: !item?.isSelected ? '#000' : '#FFF', fontSize: 14 }}>{convertForShowData(item?.name)}</Text>
                </View>
            </TouchableOpacity>
        )
    }

    const renderFooter = () => {
        if (!flatlistLoader) return null
        return (
            <View style={{
                paddingHorizontal: 20,
                paddingTop: 20,
                alignItems: 'center',
            }}>
                <ActivityIndicator size='large' color='#ee1d23' />
            </View>
        )
    }

    const renderPopupItem = ({ item }) => {
        return (
            <TouchableOpacity onPress={() => { selectPopupValue(item) }}>
                <View style={{ width: '100%', height: 40, paddingHorizontal: 10, alignItems: 'center', justifyContent: 'center', flexDirection: 'row', backgroundColor: '#f9f9f9', marginVertical: 5, borderRadius: 5 }}>
                    <Text style={{ flex: 1, color: '#000', fontSize: 14 }}>{convertForShowData(item?.label)}</Text>
                </View>
            </TouchableOpacity>
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
                        <Text style={styles._upperView._txt}>{convertForShowData(textValue.REGISTRATION)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() =>{
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}>
                            <Image style={styles._upperView._back._img} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 5 }}>
                        <ScrollView showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                            <View style={{ width: '100%', flexDirection: 'column' }}>
                                <>
                                    <Text style={{ width: '100%', textAlign: 'center', fontSize: 22, fontWeight: '600', color: '#000' }}>
                                        {textValue.Sign_Up_To_Your_Mason}
                                    </Text>
                                </>
                                <View style={{ height: 20 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.Full_name_of_mason}
                                            keyboardType='default'
                                            onChangeText={text => {
                                                const filtered = text.replace(/[^A-Za-z\s]/g, '')
                                                setName(filtered)
                                                //console.log("text--------",name);
                                                
                                            }}
                                            value={convertForShowData(name)}
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row' }}>
                                        <View style={{ width: 60, height: '100%', backgroundColor: '#FFE8E9', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{ color: '#000' }}>+91</Text>
                                        </View>
                                        <View style={{ width: 1, height: '100%', backgroundColor: '#FFDBDD' }} />
                                        <View style={{ flex: 1, height: '100%', justifyContent: 'center' }}>
                                            <TextInput
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.Enter_Mobile_Number}
                                                style={[styles._lowerView._scrollView._input._txt_input, { marginLeft: 5, width: '70%', }]}
                                                onChangeText={text => {
                                                    const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                    setPhone(filtered)
                                                }}
                                                value={convertForShowData(phone)}
                                                keyboardType='phone-pad'
                                                color='#000000'
                                            />

                                            <TouchableOpacity
                                                activeOpacity={0.8}
                                                style={[styles._lowerView._scrollView._btn, { backgroundColor: verifyText == textValue.Verified ? '#EE1D23' : '#FFFFFF' }]}
                                                onPress={() => {
                                                    if (phone) {
                                                        verifyText == textValue.Verified ? null : verifyText == textValue.Send_OTP ? send_otp() : verify_otp()
                                                    } else {
                                                        showToast('error', textValue.Phone_no_is_required)
                                                    }
                                                }}
                                            >
                                                <Text style={[styles._lowerView._scrollView._btn._txt, { color: verifyText == textValue.Verified ? '#fff' : '#000' }]}>{convertForShowData(verifyText)}</Text>
                                            </TouchableOpacity>
                                        </View>
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                {verifyText == textValue.Send_OTP || verifyText == textValue.Verified ? null : <>
                                    <View style={{ alignItems: 'center', paddingHorizontal: 40 }}>
                                        <Text style={{ fontSize: 24, fontWeight: 800, color: '#000' }}>{convertForShowData(textValue.Verify_Code)}</Text>
                                        <View style={{ height: 5 }} />
                                        <Text style={{ fontSize: 16, color: '#7B7B7B', fontWeight: 400, textAlign: 'center' }}>
                                            {convertForShowData(textValue.otp_message_1) + ' '}
                                            {convertForShowData(textValue.otp_message_2)}
                                        </Text>
                                        <View style={{ height: 25 }} />
                                    </View>
                                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center' }}>
                                        <View style={{
                                            width: '70%', height: 50,
                                            flexDirection: 'row',
                                            alignItems: 'center', justifyContent: 'center'
                                        }}>
                                            {otp.map((digit, index) => (
                                                <TextInput
                                                    key={index}
                                                    style={{
                                                        borderColor: '#a8a8a8',
                                                        borderWidth: 1,
                                                        borderRadius: 5,
                                                        textAlign: 'center',
                                                        fontSize: 18,
                                                        fontWeight: '600',
                                                        width: 40,
                                                        height: 40,
                                                        backgroundColor: '#fff',
                                                        margin: 10,
                                                        color: '#000'
                                                    }}
                                                    value={convertForShowData(digit)}
                                                    onChangeText={(text) => {
                                                        const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                        handleInputChange(filtered, index)
                                                    }}
                                                    keyboardType='number-pad'
                                                    maxLength={1}
                                                    ref={(ref) => (inputRefs[index] = ref)}
                                                    onKeyPress={(e) => handleKeyPress(e, index)}
                                                />
                                            ))}
                                        </View>
                                    </View>
                                    <View style={{ justifyContent: 'center', alignItems: 'center', position: 'relative', }}>
                                        <View style={{ alignItems: 'center' }}>
                                            <Text style={{ fontSize: 16, color: '#6A6A6A', fontWeight: 500 }}>{convertForShowData(textValue.Didnt_receive_an_OTP)}</Text>
                                            <View style={{ height: 5 }} />
                                            <TouchableOpacity activeOpacity={0.8} onPress={() => { send_otp() }}                                            >
                                                <Text style={{ color: '#EE1D23', fontSize: 16, fontWeight: '600', marginTop: 5, textDecorationStyle: 'solid', textDecorationColor: '#000', }}>{convertForShowData(textValue.Resend_OTP)}</Text>
                                            </TouchableOpacity>
                                            <View style={{ height: 25 }} />
                                        </View>
                                    </View>
                                </>}
                                <>
                                    <TouchableOpacity onPress={() => {
                                        setTypePopup(1)
                                        setDataSetList(materialStatus)
                                        setPickerPopup(true)
                                    }} style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                        <Text style={{ flex: 1, color: '#000' }}>{label == '' ? textValue.Marital_Status : label}</Text>
                                        <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                                        <View style={{ width: 10 }} />
                                    </TouchableOpacity>
                                </>
                                <View style={{ height: 10 }} />
                                {valueMarage == 1 ? <>
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                style={{ width: '100%' }}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.Spouse_name}
                                                onChangeText={text => {
                                                    const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                    setSpouse(filtered)
                                                }}
                                                value={convertForShowData(spouse)}
                                                color='#000000'
                                            />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, flexDirection: 'row', paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{
                                                flex: 1,
                                                color: '#000000'
                                            }}>{convertForShowData(spouseDob)}</Text>
                                            <TouchableOpacity
                                                onPress={() => {
                                                    showDatePicker('spouse')
                                                }}
                                                style={{
                                                    height: '100%',
                                                    width: 50,
                                                    justifyContent: 'center',
                                                    alignItems: 'center',
                                                }}>
                                                <Image style={styles._lowerView._scrollView._dob._img} source={Icons.calender} />
                                            </TouchableOpacity>
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                </> : null}
                                <>
                                    <TouchableOpacity onPress={() => {
                                        setTypePopup(2)
                                        setDataSetList(branchList)
                                        setPickerPopup(true)
                                    }} style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                        <Text style={{ flex: 1, color: '#000' }}>{branchDropdownLabel == '' ? textValue.Select_branch : convertForShowData(branchDropdownLabel)}</Text>
                                        <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                                        <View style={{ width: 10 }} />
                                    </TouchableOpacity>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <TouchableOpacity onPress={() => {
                                        setDealerRssdListPopup(true)
                                    }} style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                        <Text style={{ flex: 1, color: '#000' }} numberOfLines={2}>{selectedDealerRssdListTitle == '' ? textValue.Select_Dealer_Rssd_Sub_Dealer : convertForShowData(selectedDealerRssdListTitle)}</Text>
                                        <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                                        <View style={{ width: 10 }} />
                                    </TouchableOpacity>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 120, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD' }}>
                                        <TextInput
                                            style={{ top: 5 }}
                                            multiline={true}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.Address_1}
                                            onChangeText={text => {
                                                const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                setAddress1(filtered)
                                            }}
                                            value={convertForShowData(address1)}
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 120, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD' }}>
                                        <TextInput
                                            style={{ top: 5 }}
                                            multiline={true}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.Address_2}
                                            onChangeText={text => {
                                                const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                setAddress2(filtered)
                                            }}
                                            value={convertForShowData(address2)}
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.City}
                                            onChangeText={text => {
                                                const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                setCity(filtered)
                                            }}
                                            value={convertForShowData(city)}
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.District}
                                            onChangeText={text => {
                                                const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                setDistrict(filtered)
                                            }}
                                            value={convertForShowData(district)}
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.State}
                                            onChangeText={text => {
                                                const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                setState(filtered)
                                            }}
                                            value={convertForShowData(state)}
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.Country}
                                            onChangeText={text => {
                                                const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                setCountryName(filtered)
                                            }}
                                            value={convertForShowData(countryName)}
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.Pin}
                                            onChangeText={text => {
                                                const filtered = text.replace(/[^A-Za-z0-9\/,\.& ]/g, '')

                                                setPin(filtered)
                                            }}
                                            value={convertForShowData(pin)}
                                            keyboardType='number-pad'
                                            maxLength={6}
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <Text style={{ fontSize: 14, color: '#000' }}>{convertForShowData(textValue.DATE_OF_BIRTH)}</Text>
                                    <View style={{ height: 5 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', flexDirection: 'row', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{ flex: 1, color: '#000000' }}>{convertForShowData(selectedDate)}</Text>
                                            <TouchableOpacity
                                                onPress={() => {
                                                    showDatePicker('mason')
                                                }}
                                                style={{
                                                    height: '100%',
                                                    width: 50,
                                                    justifyContent: 'center',
                                                    alignItems: 'center',
                                                }}>
                                                <Image style={styles._lowerView._scrollView._dob._img} source={Icons.calender} />
                                            </TouchableOpacity>
                                        </View>
                                    </>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                        <TextInput
                                            style={{ width: '100%' }}
                                            placeholderTextColor='#a8a8a8'
                                            placeholder={textValue.Enter_Aadhar_number}
                                            onChangeText={text => {
                                                if (text != '') {
                                                    if (/^\d+$/.test(text)) {
                                                        setAadhaar(text)
                                                    } else {
                                                        ToastAndroid.show(
                                                            'No special symbol allowed in Aadhar number.',
                                                            ToastAndroid.SHORT
                                                        )
                                                    }
                                                } else {
                                                    setAadhaar(text)
                                                }
                                            }}
                                            value={convertForShowData(aadhaar)}
                                            maxLength={12}
                                            keyboardType='number-pad'
                                            color='#000000'
                                        />
                                    </View>
                                </>
                                <View style={{ height: 10 }} />
                                <>
                                    <View style={{ width: '100%', height: 50, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row' }}>
                                        <View style={{ flex: 1, height: '100%', justifyContent: 'center', alignItems: 'center', flexDirection: 'row' }}>
                                            <Text style={{ flex: 1, color: '#000000', height: '100%', textAlignVertical: 'center', paddingHorizontal: 15 }}>{fileName == '' ? convertForShowData(textValue.Upload_Aadhaar) : convertForShowData(fileName)}</Text>
                                            <TouchableOpacity
                                                activeOpacity={0.8}
                                                style={[styles._lowerView._scrollView._btn, { backgroundColor: verifyText == textValue.Verified ? '#EE1D23' : '#FFFFFF' }]}
                                                onPress={() => {
                                                    setFilePickFor('aadhaar')
                                                    setImgPickup(true)
                                                }}
                                            >
                                                <Text style={[styles._lowerView._scrollView._btn._txt, { color: verifyText == textValue.Verified ? '#fff' : '#000' }]}>{convertForShowData(textValue.Choose_File)}</Text>
                                            </TouchableOpacity>
                                        </View>
                                    </View>
                                </>

                                {isVoterRequire && <>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                style={{ width: '100%' }}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.Enter_Voter_number}
                                                onChangeText={text => {
                                                    if (text != '') {
                                                        if (/^[a-zA-Z0-9]+$/.test(text)) {
                                                            setVoter(text)
                                                        } else {
                                                            ToastAndroid.show(
                                                                'No special symbol allowed in Voter number.',
                                                                ToastAndroid.SHORT
                                                            )
                                                        }
                                                    } else {
                                                        setVoter(text)
                                                    }
                                                }}
                                                value={convertForShowData(voter)}
                                                maxLength={10}
                                                color='#000000'
                                            />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row' }}>
                                            <View style={{ flex: 1, height: '100%', justifyContent: 'center', alignItems: 'center', flexDirection: 'row' }}>
                                                <Text style={{ flex: 1, color: '#000000', height: '100%', textAlignVertical: 'center', paddingHorizontal: 15 }}>{voterFileName == '' ? convertForShowData(textValue.Upload_Voter) : convertForShowData(voterFileName)}</Text>
                                                <TouchableOpacity
                                                    activeOpacity={0.8}
                                                    style={[styles._lowerView._scrollView._btn, { backgroundColor: verifyText == textValue.Verified ? '#EE1D23' : '#FFFFFF' }]}
                                                    onPress={() => {
                                                        setFilePickFor('voter')
                                                        setImgPickup(true)
                                                    }}
                                                >
                                                    <Text style={[styles._lowerView._scrollView._btn._txt, { color: verifyText == textValue.Verified ? '#fff' : '#000' }]}>{convertForShowData(textValue.Choose_File)}</Text>
                                                </TouchableOpacity>
                                            </View>
                                        </View>
                                    </>
                                </>}

                                <View style={{ height: 10 }} />
                                <>
                                    <TouchableOpacity
                                        onPress={() => {
                                            form_validation()
                                        }}
                                        style={styles._lowerView._btn}>
                                        <Text style={styles._lowerView._btn._txt}>{convertForShowData(textValue.Submit)}</Text>
                                    </TouchableOpacity>
                                </>
                                <View style={{ height: 10 }} />
                            </View>
                        </ScrollView>
                    </View>
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
            {dealerRssdListPopup ?
                <View style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0006' }}>
                    <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                        <TouchableOpacity onPress={() => setDataAndClosePopup()} style={{ minHeight: 120, flex: 1 }} />
                        <View style={{ width: '100%', minHeight: isKeyboardVisible ? 200 + keyboardHeight : 200, paddingHorizontal: 20, paddingTop: 20, flexDirection: 'column', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                            <View style={{ width: '100%', flexDirection: 'row-reverse' }}>
                                <TouchableOpacity onPress={() => { setDataAndClosePopup() }}>
                                    <Text style={{ fontSize: 16, color: '#000' }}>X</Text>
                                </TouchableOpacity>
                            </View>
                            {flatlistLoader ? <>
                                <View style={{ width: '100%', flexDirection: 'row-reverse' }}>
                                    <ActivityIndicator size={20} color='#ee1d23' />
                                    <View style={{ width: 10 }} />
                                    <Text style={{ color: '#ee1d23' }}>{convertForShowData(textValue.Loading)}...</Text>
                                </View>
                                <View style={{ height: 5 }} />
                            </> : null}
                            <View style={{ height: 10 }} />
                            <View style={{ width: '100%', backgroundColor: '#FFE8E9', padding: 15, borderTopLeftRadius: 10, borderTopRightRadius: 10 }}>
                                <View style={{ width: '100%', height: 45, backgroundColor: '#FFF', borderRadius: 10, paddingHorizontal: 20, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                    <TextInput style={{ flex: 1, height: '100%', color: '#000' }} placeholder={textValue.Search} placeholderTextColor={'#bbb'} onChangeText={searchDealer} />
                                    <View style={{ width: 1, height: '70%', backgroundColor: '#FFE8E9' }} />
                                    <View style={{ width: 15 }} />
                                    <Image style={{ width: 20, height: 20, tintColor: '#000' }} source={Icons.search} />
                                </View>
                                <View style={{ height: 10 }} />
                                <FlatList
                                    style={{ maxHeight: Dimensions.get('screen').height - 400, minHeight: 200 }}
                                    data={searchDealerRssdList}
                                    renderItem={renderItem}
                                    showsHorizontalScrollIndicator={false}
                                    showsVerticalScrollIndicator={false}
                                    ListFooterComponent={renderFooter}
                                    keyExtractor={item => item.id}
                                />
                            </View>
                        </View>
                    </View>
                </View> : null}
            {pickerPopup ? <View style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0006' }}>
                <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                    <TouchableOpacity onPress={() => setPickerPopup(false)} style={{ minHeight: 120, flex: 1 }} />
                    <View style={{ width: '100%', minHeight: isKeyboardVisible ? 200 + keyboardHeight : 200, paddingHorizontal: 20, paddingTop: 20, flexDirection: 'column', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                        <View style={{ width: '100%', backgroundColor: '#FFE8E9', padding: 15, borderTopLeftRadius: 10, borderTopRightRadius: 10 }}>
                            <FlatList
                                style={{ maxHeight: Dimensions.get('screen').height - 400, minHeight: 200 }}
                                data={dataSetList}
                                renderItem={renderPopupItem}
                                keyExtractor={item => item.id}
                            />
                        </View>
                    </View>
                </View>
            </View> : null}
            <Toast />
        </SafeView>
    )
}

export default MasonRegistration
