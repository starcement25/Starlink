import React, { useEffect, useState } from 'react'
import { Dimensions, FlatList, Image, Platform, ScrollView, Text, TextInput, TouchableOpacity, View } from 'react-native'
import DateTimePickerModal from 'react-native-modal-datetime-picker'
import Toast from 'react-native-toast-message'
import moment from 'moment'
import Icons from '../../../helper/image/ImageList'
import Loader from '../../../components/loader/Loader'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import ImagePath from '../../../image/ImagePath'
import DataStore from '../../../helper/constants/DataStore'
import Camera from '../../../components/camera/Camera'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { postApiWithHeader } from '../../../helper/http/Api'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
var dobType = ''
const UpdateMassonProfile = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()
    const [loading, setLoading] = useState(false)

    const [name, setName] = useState(DataStore.massonObj.name == null ? '' : DataStore.massonObj.name)
    const [phone, setPhone] = useState(DataStore.massonObj.phone == null ? '' : DataStore.massonObj.phone)
    const [dob, setDob] = useState(DataStore.massonObj.dob == null ? '' : DataStore.massonObj.dob)

    const [valueMarage, setValueMarage] = useState(DataStore.massonObj.marital_status_value == null ? '' : DataStore.massonObj.marital_status_value)
    const [maritalStatus, setMaritalStatus] = useState(DataStore.massonObj.marital_status == null ? '' : DataStore.massonObj.marital_status)
    const [spouse, setSpouse] = useState(DataStore.massonObj.spouse_name == null ? '' : DataStore.massonObj.spouse_name)
    const [spouseDob, setSpouseDob] = useState(DataStore.massonObj.spouse_dob == null ? 'yyyy-mm-dd' : DataStore.massonObj.spouse_dob)

    const [address1, setAddress1] = useState(DataStore.massonObj.address1 == null ? '' : DataStore.massonObj.address1)
    const [city, setCity] = useState(DataStore.massonObj.city == null ? '' : DataStore.massonObj.city)
    const [district, setDistrict] = useState(DataStore.massonObj.district == null ? '' : DataStore.massonObj.district)
    const [state, setState] = useState(DataStore.massonObj.state == null ? '' : DataStore.massonObj.state)
    const [countryName, setCountryName] = useState(DataStore.massonObj.country == null ? '' : DataStore.massonObj.country)
    const [pin, setPin] = useState(DataStore.massonObj.pincode == null ? '' : DataStore.massonObj.pincode)

    const [aadhaar, setAadhaar] = useState(DataStore.massonObj.aadhaar_no == null ? '' : DataStore.massonObj.aadhaar_no)
    const [imgPickup, setImgPickup] = useState(false)
    const [image, setImage] = useState('')
    const [fileName, setFileName] = useState(DataStore.massonObj.aadhaar_doc == null ? '' : DataStore.massonObj.aadhaar_doc)
    const [type, setType] = useState('')
    const [isImageTaken, setIsImageTaken] = useState(false)
    const [isImagePick, setIsImagePick] = useState(false)

    const [materialStatus, setMaterialStatus] = useState()
    const [dataSetList, setDataSetList] = useState([])
    const [pickerPopup, setPickerPopup] = useState(false)
    const [isDatePickerVisible, setDatePickerVisibility] = useState(false)

    useEffect(() => {
        setLoading(false)
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
        if (DataStore.massonObj.aadhaar_doc == '') {
            setIsImageTaken(false)
        } else {
            setIsImageTaken(true)
        }
    }, [])

    const selectPopupValue = (data) => {
        setValueMarage(data.value)
        setMaritalStatus(data.label)
        setPickerPopup(false)
    }

    const showDatePicker = (type) => {
        dobType = type
        setDatePickerVisibility(true)
    }

    const handleConfirm = (date) => {
        if (dobType == 'mason') {
            setDob(moment(date).format('DD/MM/yyyy'))
        } else {
            setSpouseDob(moment(date).format('DD/MM/yyyy'))
        }
        hideDatePicker()
    }

    const hideDatePicker = () => {
        setDatePickerVisibility(false)
    }

    function getImage(data) {
        setImgPickup(!imgPickup)
        if (data != null) {
            try {
                setIsImagePick(true)
                setIsImageTaken(false)
                setImage(data.res.assets[0].uri)
                setFileName(data.res.assets[0].fileName)
                setType(data.res.assets[0].type)
            } catch (err) { }
        }
    }

    const checkValidation = () => {
        if (spouse == '' && valueMarage == 1) {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t45 })
        } else if (spouseDob == '' && valueMarage == 1) {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t46 })
        } else if (address1 == '') {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t18 })
        } else if (city == '') {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t19 })
        } else if (district == '') {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t20 })
        } else if (state == '') {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t21 })
        } else if (countryName == '') {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t22 })
        } else if (pin == '') {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t23 })
        } else if (pin.length != 6) {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t23 })
        } else if (aadhaar == '') {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t25 })
        } else if (aadhaar.length != 12) {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t26 })
        } else if (fileName == '') {
            Toast.show({ type: 'error', text1: textValue.sorry, text2: messageList.t27 })
        } else {
            requestForUpdateMasonData()
        }
    }

    const requestForUpdateMasonData = () => {
        setLoading(true)
        var FormData = require('form-data')
        var formdata = new FormData()
        formdata.append('address1', convertForUploadData(address1))
        formdata.append('city', convertForUploadData(city))
        formdata.append('state', convertForUploadData(state))
        formdata.append('district', convertForUploadData(district))
        formdata.append('country', convertForUploadData(countryName))
        formdata.append('pincode', convertForUploadData(pin))
        formdata.append('marital_status', valueMarage)
        formdata.append('spouse_name', convertForUploadData(spouse))
        formdata.append('spouse_dob', spouseDob == 'yyyy-mm-dd' ? '' : convertForUploadData(spouseDob))
        formdata.append('dob', convertForUploadData(dob))
        if (isImagePick) {
            formdata.append('aadhaar_doc', { uri: image, name: fileName, type: type })
        }
        formdata.append('aadhaar_no', convertForUploadData(aadhaar))
        formdata.append('preferred_app_lang', selectedLanguage())

        //console.log(formdata, valueMarage)

        postApiWithHeader('te/update-mason/' + DataStore.massonObj.id, formdata)
            .then(response => {
                //console.log(response.data)
                if (response.data.status) {
                    Toast.show({ type: 'success', text1: messageList.success, text2: response.data.msg })
                } else {
                    Toast.show({ type: 'error', text1: textValue.sorry, text2: response.data.msg })
                }
                setLoading(false)
                if (response.data.status) {
                    props.navigation.goBack()
                }
            })
            .catch(err => {
                setLoading(false)
                //console.log(err)
            })
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
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{textValue.Updated_Profile}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {
                                                    setTimeout(()=>{
                                                        props.navigation.goBack()
                                                    },500)
                                                }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 5 }}>
                        <ScrollView showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                            <View style={{ width: '100%', flexDirection: 'column' }}>
                                <>
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                style={{ width: '100%' }}
                                                placeholderTextColor='#a8a8a8'
                                                editable={false}
                                                placeholder={textValue.Full_name_of_mason}
                                                onChangeText={text => { setName(text) }}
                                                value={convertForShowData(name)}
                                                color='#000000' />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.Enter_Mobile_Number}
                                                style={{ width: '100%' }}
                                                editable={false}
                                                onChangeText={text => { setPhone(text) }}
                                                maxLength={10}
                                                value={convertForShowData(phone)}
                                                keyboardType='phone-pad'
                                                color='#000000' />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, flexDirection: 'row', paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{ flex: 1, color: '#000000' }}>{convertForShowData(dob)}</Text>
                                            <TouchableOpacity onPress={() => { }} style={{ height: '100%', width: 50, justifyContent: 'center', alignItems: 'center', }}>
                                                <Image style={{ height: 20, width: 20 }} source={Icons.calender} />
                                            </TouchableOpacity>
                                        </View>
                                    </>
                                </>

                                <View style={{ height: 10 }} />

                                <>
                                    <>
                                        <TouchableOpacity onPress={() => {
                                            setDataSetList(materialStatus)
                                            setPickerPopup(true)
                                        }} style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                            <Text style={{ flex: 1, color: '#000' }}>{maritalStatus == '' ? textValue.Marital_Status : maritalStatus}</Text>
                                            <Image source={ImagePath.DownArrowBlackIcon} style={{ width: 15, height: 10, resizeMode: 'contain', marginTop: 5 }} />
                                            <View style={{ width: 10 }} />
                                        </TouchableOpacity>
                                    </>
                                    {valueMarage == 1 ? <>
                                        <View style={{ height: 10 }} />
                                        <>
                                            <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                                <TextInput
                                                    style={{ width: '100%' }}
                                                    placeholderTextColor='#a8a8a8'
                                                    editable={DataStore.massonObj.marital_status_value == 1 ? false : true}
                                                    placeholder={textValue.Spouse_name}
                                                    onChangeText={text => { setSpouse(text) }}
                                                    value={convertForShowData(spouse)}
                                                    color='#000000' />
                                            </View>
                                        </>
                                        <View style={{ height: 10 }} />
                                        <>
                                            <View style={{ width: '100%', height: 50, flexDirection: 'row', paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                                <Text style={{ flex: 1, color: '#000000' }}>{convertForShowData(spouseDob)}</Text>
                                                <TouchableOpacity onPress={() => {
                                                    if (DataStore.massonObj.marital_status_value == 0) {
                                                        showDatePicker('spouse')
                                                    }
                                                }} style={{ height: '100%', width: 50, justifyContent: 'center', alignItems: 'center', }}>
                                                    <Image style={{ height: 20, width: 20 }} source={Icons.calender} />
                                                </TouchableOpacity>
                                            </View>
                                        </>
                                    </> : null}
                                </>

                                <View style={{ height: 10 }} />

                                <>
                                    <>
                                        <View style={{ width: '100%', height: 120, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD' }}>
                                            <TextInput
                                                style={{ top: 5 }}
                                                multiline={true}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.Address_1}
                                                onChangeText={text => { setAddress1(text) }}
                                                value={convertForShowData(address1)}
                                                color='#000000' />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                style={{ width: '100%' }}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.City}
                                                onChangeText={text => { setCity(text) }}
                                                value={convertForShowData(city)}
                                                color='#000000' />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                style={{ width: '100%' }}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.District}
                                                onChangeText={text => { setDistrict(text) }}
                                                value={convertForShowData(district)}
                                                color='#000000' />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                style={{ width: '100%' }}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.State}
                                                onChangeText={text => { setState(text) }}
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
                                                onChangeText={text => { setCountryName(text) }}
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
                                                onChangeText={text => { setPin(text) }}
                                                value={convertForShowData(pin)}
                                                keyboardType='number-pad'
                                                maxLength={6}
                                                color='#000000'
                                            />
                                        </View>
                                    </>
                                </>

                                <View style={{ height: 10 }} />

                                <>
                                    <>
                                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                                            <TextInput
                                                style={{ width: '100%' }}
                                                placeholderTextColor='#a8a8a8'
                                                placeholder={textValue.Enter_Aadhar_number}
                                                editable={DataStore.massonObj.aadhaar_no.length == 12 ? false : true}
                                                onChangeText={text => { setAadhaar(text) }}
                                                value={convertForShowData(aadhaar)}
                                                maxLength={12}
                                                keyboardType='number-pad'
                                                color='#000000'
                                            />
                                        </View>
                                    </>
                                    <View style={{ height: 10 }} />
                                    <TouchableOpacity onPress={() => { setImgPickup(true) }} activeOpacity={0.8}>
                                        {isImageTaken ? <View style={{ width: '100%', height: 160, backgroundColor: '#FFF5F6', borderRadius: 5, padding: 3, borderWidth: 1, borderColor: '#FFDBDD' }}>
                                            <Image source={{ uri: DataStore.massonObj.aadhaar_doc }} style={{ width: '100%', height: '100%', borderRadius: 5, resizeMode: 'contain' }} />
                                        </View> : <>
                                            {isImagePick ? <View style={{ width: '100%', height: 160, backgroundColor: '#FFF5F6', borderRadius: 5, padding: 3, borderWidth: 1, borderColor: '#FFDBDD' }}>
                                                <Image source={{ uri: image }} style={{ width: '100%', height: '100%', borderRadius: 5, resizeMode: 'contain' }} />
                                            </View> : <View style={{ width: '100%', height: 160, backgroundColor: '#FFF5F6', borderRadius: 5, padding: 3, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center', flexDirection: 'column' }}>
                                                <Image source={require('../../../image/profile.png')} style={{ width: 70, height: 70 }} />
                                                <Text style={{ fontSize: 12, color: '#333' }}>Aadhaar image upload</Text>
                                            </View>}
                                        </>}
                                    </TouchableOpacity>
                                </>

                                <View style={{ height: 10 }} />

                                <>
                                    <TouchableOpacity onPress={() => { checkValidation() }} style={{ backgroundColor: '#ee1d23', height: 45, width: '100%', borderRadius: 25, justifyContent: 'center', alignItems: 'center', }}>
                                        <Text style={{ color: '#fff', fontWeight: '600', fontSize: 20 }}>{convertForShowData(textValue.Submit)}</Text>
                                    </TouchableOpacity>
                                </>

                                <View style={{ height: 20 }} />
                            </View>
                        </ScrollView>
                    </View>
                </View>
            </View>
            {pickerPopup ? <View style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0006' }}>
                <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                    <TouchableOpacity onPress={() => setPickerPopup(false)} style={{ minHeight: 120, flex: 1 }} />
                    <View style={{ width: '100%', paddingHorizontal: 20, paddingTop: 20, flexDirection: 'column', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                        <View style={{ width: '100%', backgroundColor: '#FFE8E9', padding: 15, borderTopLeftRadius: 10, borderTopRightRadius: 10 }}>
                            <FlatList
                                style={{ maxHeight: Dimensions.get('screen').height - 400, minHeight: 100 }}
                                data={dataSetList}
                                renderItem={renderPopupItem}
                                keyExtractor={item => item.id}
                            />
                        </View>
                    </View>
                </View>̵
            </View> : null}
            {imgPickup ? <Camera sendData={getImage} /> : null}
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
        </SafeView>
    )
}

export default UpdateMassonProfile