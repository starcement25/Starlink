import React, { useEffect, useState } from 'react'
import { ScrollView, Text, TextInput, View, Image, TouchableOpacity, Linking, Platform } from 'react-native'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import Icon from 'react-native-vector-icons/dist/Feather'
import Camera from '../../../components/camera/Camera'
import Loader from '../../../components/loader/Loader'
import { getApiWithHeader, postApiWithHeader } from '../../../helper/http/Api'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import Constants from '../../../helper/constants/Constants'

const EditProfile = (props) => {
    //console.log("🔥 EditProfile SCREEN RENDERED");
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(true)
    const [userInfo, setUserInfo] = useState('')
    const [imgPickup, setImgPickup] = useState(false)
    const [image, setImage] = useState('')
    const [name, setName] = useState('')
    const [phone, setPhone] = useState('')
    const [mail, setMail] = useState('')
    const [aadhaar, setAadhaar] = useState('')
    const [employeeId, setEmployeeId] = useState('')
    const [dealerRssdCode, setDealerRssdCode] = useState('')
    const [linkedTE, setLinkedTE] = useState('')
    const [tEMobile, setTEMobile] = useState('')
    const [masonCategory, setMasonCategory] = useState('')
    const [pickImageFor, setPickImageFor] = useState('')

    useEffect(() => {
        requestForProfileDetails()
    }, [])

    function getImage(data) {
        if (pickImageFor == 'profile_image') {
            setImgPickup(!imgPickup)
            if (data != null) {
                try {
                    setImage(data.res.assets[0].uri)
                    requestForChangeProfilePic(data.res.assets[0].uri, data.res.assets[0].fileName, data.res.assets[0].type)
                } catch (err) { }
            }
        } else {
            setImgPickup(!imgPickup)
        }
    }

    const requestForProfileDetails = () => {
        getApiWithHeader(Constants.my_profile + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                setLoading(false)
                //console.log("profile--",response.data)

                if (response.data.status) {
                    setUserInfo(response?.data)
                    setName(response?.data?.data?.name)
                    setPhone(response?.data?.data?.phone)
                    setMail(response?.data?.data?.email)
                    setAadhaar(response?.data?.data?.aadhaar_no)

                    setLinkedTE(response?.data?.data?.te?.name)
                    setTEMobile(response?.data?.data?.te?.phone)

                    setDealerRssdCode(response?.data?.data?.emp_code)
                    setEmployeeId(response?.data?.data?.emp_code)
                    setMasonCategory(response?.data?.data?.mason_category?.name)
                    storeData(response.data)
                } else {
                    if (response?.data?.status_code == 401) {
                        showToast('error', messageList.error, response?.data?.message)
                        requestForLogout()
                    } else {
                        showToast('error', messageList.error, response?.data?.msg)
                    }
                }
            })
            .catch(err => {

                setLoading(false)
                showToast('error', messageList.error, messageList.t4)
            })
    }

    const storeData = async (value) => {
        try {
            await AsyncStorage.setItem('user_info', JSON.stringify(value))
        } catch (e) { }
    }

    const showToast = (type, title, msg) => {
        Toast.show({
            type: type,
            text1: title,
            text2: msg,
            text2NumberOfLines: 2
        })
    }

    const requestForChangeProfilePic = (image, fileName, type) => {
        setLoading(true)
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('pic', {
            uri: image,
            name: fileName,
            type: type,
        }
        )
        formData.append('preferred_app_lang', selectedLanguage())
        postApiWithHeader(constants.requestForChangeProfilePic, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    showToast('success', messageList.success, response.data.msg)
                } else {
                    showToast('error', messageList.error, response.data.msg)
                }
                newProfileDetailsSetInStore()
            })
            .catch(err => {
                setLoading(false)
                showToast('error', messageList.error, messageList.t4)
            })
    }

    const newProfileDetailsSetInStore = async () => {
        try {
            await AsyncStorage.removeItem('user_info')
            requestForProfileDetails()
        } catch (e) { }
    }

    const requestForLogout = async () => {
        try {
            await AsyncStorage.removeItem('user_info')
            props.navigation.navigate('AuthStack')
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
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{convertForShowData(textValue.Edit_Profile)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, width: '100%', flexDirection: 'row', alignItems: 'center', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => props.navigation.navigate('Dashboard')}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                        <View style={{ flex: 1 }} />
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                        <View style={{ width: '100%', justifyContent: 'center', alignItems: 'center', }}>
                            <View style={{ height: 120, width: 120, backgroundColor: 'blue', borderRadius: 60, position: 'relative', }}>
                                {image ? <Image style={{ height: 120, width: 120, borderColor: '#ee1d23', borderRadius: 60, borderWidth: 2 }} source={{ uri: image }} />
                                    : <Image style={{ height: 120, width: 120, borderColor: '#ee1d23', borderRadius: 60, borderWidth: 2 }} source={userInfo?.data?.profile_pic == null ? Icons.avatar : { uri: userInfo?.data?.profile_pic }} />}
                                {/* + '?t=' + moment().valueOf() */}
                                <TouchableOpacity
                                    activeOpacity={0.8}
                                    onPress={() => {
                                        setPickImageFor('profile_image')
                                        setImgPickup(true)
                                    }}
                                    style={{ height: 35, width: 35, backgroundColor: '#FFF0F1', borderRadius: 50, position: 'absolute', bottom: 0, right: 0, justifyContent: 'center', alignItems: 'center', }}>
                                    <Image style={{ height: 18, width: 18, resizeMode: 'contain' }} source={ImagePath.CameraIcon} />
                                </TouchableOpacity>
                            </View>
                        </View>
                        <View style={{ height: 20 }} />
                        <View style={{ width: '100%' }}>
                            <ScrollView style={{ width: '100%' }} showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                                <View style={{ width: '100%', flexDirection: 'column' }}>
                                    <View style={{ width: '100%' }}>
                                        <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                            <TextInput
                                                placeholderTextColor='#a8a8a8'
                                                placeholder=''
                                                style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                onChangeText={text => setName(text)}
                                                value={convertForShowData(name)}
                                                editable={false}
                                                selectTextOnFocus={false}
                                            />
                                        </View>
                                        <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                            <Text>{' ' + convertForShowData(textValue.Your_Name) + ' '}</Text>
                                        </View>
                                    </View>

                                    <View style={{ height: 10 }} />

                                    <View style={{ width: '100%' }}>
                                        <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                            <TextInput
                                                placeholderTextColor='#a8a8a8'
                                                placeholder=''
                                                style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                onChangeText={text => setPhone(text)}
                                                value={convertForShowData(phone)}
                                                editable={false}
                                                selectTextOnFocus={false}
                                            />
                                        </View>
                                        <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                            <Text>{' ' + convertForShowData(textValue.Your_Phone_no) + ' '}</Text>
                                        </View>
                                    </View>

                                    <View style={{ height: 10 }} />

                                    <View style={{ width: '100%' }}>
                                        <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                            <TextInput
                                                placeholderTextColor='#a8a8a8'
                                                placeholder=''
                                                style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                onChangeText={text => setMail(text)}
                                                value={mail}
                                                editable={false}
                                                selectTextOnFocus={false}
                                            />
                                        </View>
                                        <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                            <Text>{' ' + convertForShowData(textValue.Your_Mail) + ' '}</Text>
                                        </View>
                                    </View>

                                    <View style={{ height: 10 }} />

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    onChangeText={text => setLinkedTE(text)}
                                                    value={convertForShowData(linkedTE)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Technical_Engineer_Name) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15, flex: 1 }}
                                                    onChangeText={text => setTEMobile(text)}
                                                    value={convertForShowData(tEMobile)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                                <TouchableOpacity
                                                    onPress={() => {
                                                        Linking.openURL(`tel:${'+91' + tEMobile}`)
                                                    }}
                                                    style={{ width: 30, height: 30, justifyContent: 'center', alignItems: 'center' }}>
                                                    <Icon name='phone-call' size={20} />
                                                </TouchableOpacity>
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Technical_Engineer_Mobile) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    onChangeText={text => setAadhaar(text)}
                                                    value={convertForShowData(aadhaar)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Aadhaar_no) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    onChangeText={text => setMasonCategory(text)}
                                                    value={convertForShowData(masonCategory)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Categoty) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    value={convertForShowData(userInfo?.data?.address1)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Address_1) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    value={convertForShowData(userInfo?.data?.address2)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Address_2) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    value={convertForShowData(userInfo?.data?.city)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.City) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    value={convertForShowData(userInfo?.data?.district)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.District) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    value={convertForShowData(userInfo?.data?.state)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.State) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    value={convertForShowData(userInfo?.data?.country)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Country) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 2 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    value={convertForShowData(userInfo?.data?.pincode)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Pin) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}

                                    {userInfo?.data?.role == 1 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    onChangeText={text => setEmployeeId(text)}
                                                    value={convertForShowData(employeeId)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + convertForShowData(textValue.Employee_Id1) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}
                                    
                                    {userInfo?.data?.role == 3 || userInfo?.data?.role == 4 ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                        <View style={{ width: '100%' }}>
                                            <View style={{ width: '100%', marginTop: 8, borderRadius: 10, borderWidth: 1, borderColor: '#FFDFE1', flexDirection: 'row' }}>
                                                <TextInput
                                                    placeholderTextColor='#a8a8a8'
                                                    placeholder=''
                                                    style={{ fontWeight: '500', fontSize: 16, height: 55, color: '#000', paddingHorizontal: 15 }}
                                                    onChangeText={text => setDealerRssdCode(text)}
                                                    value={convertForShowData(dealerRssdCode)}
                                                    editable={false}
                                                    selectTextOnFocus={false}
                                                />
                                            </View>
                                            <View style={{ marginLeft: 20, backgroundColor: '#FFF', position: 'absolute' }}>
                                                <Text>{' ' + userInfo?.data?.role == 3 ? convertForShowData(textValue.Dealer_Code) : convertForShowData(textValue.Rssd_Code) + ' '}</Text>
                                            </View>
                                        </View>
                                        <View style={{ height: 10 }} />
                                    </View> : null}
                                    <View style={{ height: 120 }} />
                                </View>
                            </ScrollView>
                        </View>
                    </View>
                </View>
            </View>
            {imgPickup ? <Camera sendData={getImage} /> : null}
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default EditProfile