import React, { useCallback, useEffect, useState } from 'react'
import { ScrollView, Text, View, Image, TouchableOpacity, BackHandler, Linking, Dimensions, TouchableWithoutFeedback, Modal } from 'react-native'
import { CommonActions, useFocusEffect } from '@react-navigation/native'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import { SliderBox } from 'react-native-image-slider-box'
import styles from './DashboardStyle'
import Loader from '../../../components/loader/Loader'
import { getApiWithHeader, postApiWithHeader, postApiWithHeaderBody } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Toolbar from '../../../components/toolbar/Toolbar'
import LanguagePicker from '../../../components/language/LanguagePicker'
import DataStore from '../../../helper/constants/DataStore'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import ReactNativeModal from 'react-native-modal'
import Constants from '../../../helper/constants/Constants'
import BirthdayPopup from '../rewardDetails/BirthdayPopup'
import DOBOverlay from '../rewardDetails/DOBOverlay'
import { useDeleteVisible } from '../../../helper/constants/DeleteVisibleContext'

let imageList = []

const Dashboard = (props) => {
      const { setDeleteVisible } = useDeleteVisible();
    const textValue = useTextValue()
    const messageList = useMessageList()
    const [isExitPopup, setExitPopup] = useState(false)
    const [currentIndex, setCurrentIndex] = useState(0)
    const [userInfo, setUserInfo] = useState('')
    const [userProfileInfo, setProfileUserInfo] = useState('')
    const [loading, setLoading] = useState(false)
    const [socialLink, setSocialLink] = useState('')
    const [bannerLink, setBannerLink] = useState('')
    const [flagBanner, setFlagBanner] = useState(false)
    const [languagePopup, setLanguagePopup] = useState(false)
    const [firstUserTandCPopup, setFirstUserTandCPopup] = useState(false)
    const [firstUserCheckedTandCPopup, setFirstUserCheckedTandCPopup] = useState(false)
    const [secondUserTandCPopup, setSecondUserTandCPopup] = useState(false)
    const [secondUserCheckedTandCPopup, setSecondUserCheckedTandCPopup] = useState(false)
    const [otherUserTandCPopup, setOtherUserTandCPopup] = useState(false)
    const [otherUserCheckedTandCPopup, setOtherUserCheckedTandCPopup] = useState(false)
    const [termsText, setTermsText] = useState('');
    const [acknowledgementVisibility, setAcknowledgementVisibility] = useState(false)
    const [ackMsg, setAckMsg] = useState('')
    const [showDOB, setShowDOB] = useState(false);
    const [showBirthdayPopup, setShowBirthdayPopup] = useState(false);
    const [birthData, setBirthData] = useState()
    const checkBirthday = (userBirthday) => {
        if (!userBirthday) return false;

        const today = new Date();
        const birthday = new Date(userBirthday);

        return (
            today.getDate() === birthday.getDate() &&
            today.getMonth() === birthday.getMonth()
        );
    };

    const my_profile = () => {
        getApiWithHeader(constants.my_profile + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    setUserInfo(response.data)
                    getBirthday(response?.data?.data?.id)
                    //console.log("user----", response?.data)
                    setUserInfo((prevState) => ({
                        ...prevState,
                        data: {
                            ...prevState.data,
                            profile_pic: response.data.data.profile_pic,
                        },
                    }))
                    setProfileUserInfo(response.data.data.profile_pic)

                } else {
                    if (response?.data?.status_code == 401) {
                        showToast('error', messageList.error, response?.data?.message)
                        _logout()
                    } else {
                        showToast('error', messageList.error, response?.data?.msg)
                    }
                }
            })
            .catch(err => {
                setLoading(false)
                _logout()
                showToast('error', messageList.error, messageList.t4)
            })
    }

    const storeBirthday = async (dob) => {
        //console.log("dobbbb", dob);

        try {
            const token = await AsyncStorage.getItem('access_token');
            const response = await fetch(
                Constants.base_url + Constants.store_birthday,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        id: userInfo?.data?.id,
                        dob: dob,
                    }),
                }
            );

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || "Failed to store birthday");
            } else {
                getBirthday(userInfo?.data?.id)
            }
        } catch (error) {
            console.error("storeBirthday Error:", error);
            throw error;
        }
    };
    const checkAndLogBirthday = async () => {
        //console.log("id--------", userInfo?.data);
        try {
            const response = await postApiWithHeaderBody(
                Constants.check_and_log_birthday,
                {
                    id: userInfo?.data?.id,
                }
            );

            //console.log("id--------", response);

        } catch (error) {
            console.error("checkAndLogBirthday Error:", error);
            throw error;
        }
    };

    const getBirthday = async (id) => {
        try {
            const token = await AsyncStorage.getItem('access_token');
            //console.log("getBirthday URL:", `${Constants.base_url}/${Constants.get_birthday}${id}`);

            const response = await fetch(
                `${Constants.base_url}/${Constants.get_birthday}${id}`,
                {
                    method: "GET",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": `Bearer ${token}`
                    },
                }
            );

            // Read as text first to see raw response
            const data = await response.json();
            //console.log("Raw response:", data);
            //console.log("Status:", response.status);

            if (data?.status) {
                setBirthData(data)
                setShowBirthdayPopup(true)
            } else if (data?.message == "Birthday Not Found") {
                setShowDOB(true)
            }

            if (!response.ok) {
                throw new Error(data.message || "Failed to fetch birthday");
            }

            return data;
        } catch (error) {
            console.error("getBirthday Error:", error);
            throw error;
        }
    };

const deleteAccount = async () => {
  try {
    const response = await fetch(
      `${Constants.base_url}${Constants.delete_account_btn}`,
      {
        method: "GET",
      }
    );

    const data = await response.json();

    if (response.ok) {
      console.log("status:", data?.status);

      const isVisible = data?.status === 1;
        setDeleteVisible(isVisible)
      await AsyncStorage.setItem(
        "deleteVisible",
        JSON.stringify(isVisible)
      );

    } else {
      console.log("Error:", data);
      return null;
    }
  } catch (error) {
    console.error("API Error:", error);
    return null;
  }
};


    useEffect(() => {
        const focusListener = props.navigation.addListener('focus', () => { })
        return focusListener
    }, [props.navigation])

    // useEffect(() => {
    //     //console.log("user_id-", JSON.stringify(props));
    //     const focusListener = props.navigation.addListener('focus', () => {
    //         get_social_link()
    //         _banner_images()
    //         my_profile()
    //         //checkAndLogBirthday()
    //         //console.log("user_id-", props);
    //     })
    //     return focusListener
    // }, [props.navigation])

    useEffect(() => {
        const focusListener = props.navigation.addListener('blur', () => {
            //backHandler.remove()
        })
        return focusListener
    }, [props.navigation])

    useEffect(() => {
        deleteAccount()
        getBanner()
        checkUserLastOrderAcknowledgement()
        setTimeout(() => {
            getToken()
            getTermsText()
        }, 2000)
    }, [])

    const getToken = async () => {
        const token = await AsyncStorage.getItem('access_token');
        //console.log("token----------->",JSON.stringify(token))
    }

    // useEffect(() => {
    //     const backAction = () => {
    //         setExitPopup(true)
    //         return true
    //     }
    //     backHandler = BackHandler.addEventListener(
    //         'hardwareBackPress',
    //         backAction
    //     )
    //     props.navigation.addListener('focus', () => {
    //         setLoading(true)
    //         my_profile()
    //         backHandler = BackHandler.addEventListener(
    //             'hardwareBackPress',
    //             backAction
    //         )
    //     })
    //     return () => backHandler.remove()
    // }, [])

    useFocusEffect(
        useCallback(() => {
            const backAction = () => {
                setExitPopup(true)
                return true
            }

            const subscription = BackHandler.addEventListener(
                'hardwareBackPress',
                backAction
            )

            return () => subscription.remove()
        }, [])
    )

    useFocusEffect(
        useCallback(() => {
            setLoading(true)

            get_social_link()
            _banner_images()
            my_profile()

            return () => { }
        }, [])
    )

    // const my_profile = () => {
    //     getApiWithHeader(constants.my_profile + '?preferred_app_lang=' + selectedLanguage())
    //         .then(response => {
    //             setLoading(false)
    //             if (response.data.status) {
    //                 setUserInfo(response.data)
    //                 setUserInfo((prevState) => ({
    //                     ...prevState,
    //                     data: {
    //                         ...prevState.data,
    //                         profile_pic: response.data.data.profile_pic,
    //                     },
    //                 }))
    //                 setProfileUserInfo(response.data.data.profile_pic)
    //             } else {
    //                 if (response?.data?.status_code == 401) {
    //                     showToast('error', messageList.error, response?.data?.message)
    //                     _logout()
    //                 } else {
    //                     showToast('error', messageList.error, response?.data?.msg)
    //                 }
    //             }
    //         })
    //         .catch(err => {
    //             setLoading(false)
    //             _logout()
    //             showToast('error', messageList.error, messageList.t4)
    //         })
    // }

    const get_social_link = () => {
        getApiWithHeader(constants.get_social_link + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    setSocialLink(response.data.data[0])
                }
            })
            .catch(err => { })
    }

    const secure_mobile_no = () => {
        let mobile_no = userInfo?.data?.phone
        return mobile_no ? mobile_no?.substring(0, 4) : ''
    }

    const secure_mobile_last_two_digit = () => {
        let mobile_no = userInfo?.data?.phone
        return mobile_no ? mobile_no?.substring(10, 8) : ''
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

    const showToast = (type, title, msg) => {
        Toast.show({
            type: type,
            text1: title,
            text2: msg,
        })
    }

    const open_url = async (url) => {
        Linking.openURL(url)
    }

    const _banner_images = () => {
        getApiWithHeader(constants.view_banner + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                var imgs = []
                var data = response.data.data

                if (response.data.status) {
                    for (var i = 0; i < data.length; i++) {
                        imgs.push(data[i].img)
                    }
                    imageList = imgs
                }
            })
            .catch(err => { })
    }

    const getBanner = () => {
        getApiWithHeader(constants.flash_banner + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data.status) {
                    setBannerLink(response.data.data[0].banner_path)
                    setFlagBanner(true)
                }
            })
            .catch(err => {
                showToast('error', messageList.t4)
            })
    }

    const openLanguagePopup = () => {
        setLanguagePopup(true)
    }

    const saveLanguage = async (language) => {
        try {
            DataStore.language = language
            setLanguagePopup(false)
            await AsyncStorage.setItem('language_select', language)
            updateLanguage(language)
        } catch (error) { }
    }

    const updateLanguage = async (language) => {
        setLoading(true)
        var a = ''

        switch (language) {
            case 'English':
                a = 'en'
                break
            case 'Hindi':
                a = 'hi'
                break
            case 'Assamese':
                a = 'as'
                break
            case 'Bengali':
                a = 'bn'
                break
            default:
                a = 'en'
        }
        var FormData = require('form-data')
        var formData = new FormData()
        formData.append('preferred_app_lang', a)

        postApiWithHeader(constants.language_change, formData)
            .then(response => {
                setLoading(false)
                resetToDashboard()
            })
            .catch(err => { })
    }
    const handleDOBClose = useCallback(() => setShowDOB(false), []);

    const handleDOBConfirm = useCallback((date) => {
        storeBirthday(date.toISOString().split("T")[0]);
        setShowDOB(false);
    }, [userInfo]);

    const resetToDashboard = () => {
        props.navigation.dispatch(
            CommonActions.reset({
                index: 0,
                routes: [{ name: 'Dashboard' }],
            })
        )
    }

    //terms and conditions function 
    const getTermsText = async () => {
        const token = await AsyncStorage.getItem('access_token');
        const myHeaders = new Headers();
        myHeaders.append("Authorization", `Bearer ${token}`);
        myHeaders.append("Accept", "application/json")

        const requestOptions = {
            method: "GET",
            headers: myHeaders,
            redirect: "follow"
        };
        //console.log("termsTextttt----->", Constants.base_url + Constants.terms_and_conditions)
        await fetch(Constants.base_url + Constants.terms_and_conditions, requestOptions)
            .then((response) => { return response.json() })
            .then((result) => {
                ////console.log("terms------>",result)
                setTermsText(result?.data[0]?.royalty_terms_condition)
            })
            .catch((error) => console.error(error));
    }
    const checkUserLastOrderAcknowledgement = async () => {
        try {
            const token = await AsyncStorage.getItem('access_token');

            const myHeaders = new Headers();
            myHeaders.append('Authorization', `Bearer ${token}`);
            myHeaders.append('Accept', 'application/json');

            const requestOptions = {
                method: 'GET',
                headers: myHeaders,
                redirect: 'follow',
            };

            const response = await fetch(
                Constants.base_url + Constants.check_user_last_order_acknowledgement,
                requestOptions
            );

            const result = await response.json();
            //console.log('Acknowledgement Check Response --->', result);
            setAckMsg(result?.data);
            if (result?.status) setAcknowledgementVisibility(true)

        } catch (error) {
            console.error('Error checking last order acknowledgement:', error);
            throw error;
        }
    };


    const renderFirstUserTandCPopup = () => {
        return (
            <ReactNativeModal
                isVisible={firstUserTandCPopup}
                style={{ margin: 0 }}
                customBackdrop={
                    <TouchableWithoutFeedback onPress={() => { setFirstUserTandCPopup(false) }}>
                        <View style={{ flex: 1, backgroundColor: 'black' }} />
                    </TouchableWithoutFeedback>
                }>
                <View style={{ width: '100%', height: '100%', alignItems: 'center', justifyContent: 'center' }}>
                    <View style={{ width: '85%', backgroundColor: '#FFF', borderRadius: 10 }}>
                        <View style={{ width: '100%', height: 50, backgroundColor: '#e00000', borderTopLeftRadius: 10, borderTopRightRadius: 10, alignItems: 'center', justifyContent: 'center' }}>
                            <Text style={{ color: '#FFF', fontWeight: '500', fontSize: 15 }}>Terms & Condition</Text>
                        </View>
                        <Text style={{ fontSize: 14, color: '#444', paddingHorizontal: 15, paddingVertical: 10 }}>
                            {termsText}
                            {/* <Text>{' '}</Text>
                            <TouchableWithoutFeedback onPress={() => {

                            }}>
                                <Text style={{ color: '#e00000', textDecorationLine: 'underline', textDecorationColor: '#E00000' }}>Read More</Text>
                            </TouchableWithoutFeedback> */}
                        </Text>
                        <TouchableOpacity onPress={() => {
                            setFirstUserCheckedTandCPopup(!firstUserCheckedTandCPopup)
                        }} style={{ flexDirection: 'row', paddingHorizontal: 15, alignItems: 'center' }}>
                            <View style={{ width: 15, height: 15, borderWidth: 2, borderColor: '#E00000', padding: 2 }}>
                                {firstUserCheckedTandCPopup ? <View style={{ width: '100%', height: '100%', backgroundColor: '#E00000' }} /> : null}
                            </View>
                            <Text style={{ color: '#E00000', fontSize: 14, paddingLeft: 8 }}>Accept</Text>
                        </TouchableOpacity>
                        <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', paddingVertical: 10 }}>
                            <TouchableWithoutFeedback onPress={() => {
                                if (firstUserCheckedTandCPopup) {
                                    //backHandler.remove()
                                    setFirstUserTandCPopup(false)
                                    props.navigation.navigate('Gift', { obj: userInfo, user_type: userInfo.data.role == 2 ? 'mason' : 'te' })
                                } else {
                                    Toast.show({
                                        type: 'error',
                                        text2: 'Accept Terms & Condition',
                                        text2NumberOfLines: 2
                                    })
                                }
                            }}>
                                <View style={{ paddingHorizontal: 25, paddingVertical: 5, borderRadius: 5, backgroundColor: '#E00000' }}>
                                    <Text style={{ color: '#FFF', fontSize: 14 }}>Submit</Text>
                                </View>
                            </TouchableWithoutFeedback>
                        </View>
                    </View>
                    <Toast />
                </View>
            </ReactNativeModal>
        )
    }
    const renderSecondUserTandCPopup = () => {
        return (
            <ReactNativeModal
                isVisible={secondUserTandCPopup}
                style={{ margin: 0 }}
                customBackdrop={
                    <TouchableWithoutFeedback onPress={() => { setSecondUserTandCPopup(false) }}>
                        <View style={{ flex: 1, backgroundColor: 'black' }} />
                    </TouchableWithoutFeedback>
                }>
                <View style={{ width: '100%', height: '100%', alignItems: 'center', justifyContent: 'center' }}>
                    <View style={{ width: '85%', backgroundColor: '#FFF', borderRadius: 10 }}>
                        <View style={{ width: '100%', height: 50, backgroundColor: '#e00000', borderTopLeftRadius: 10, borderTopRightRadius: 10, alignItems: 'center', justifyContent: 'center' }}>
                            <Text style={{ color: '#FFF', fontWeight: '500', fontSize: 15 }}>Terms & Condition</Text>
                        </View>
                        <Text style={{ fontSize: 14, color: '#444', paddingHorizontal: 15, paddingVertical: 10 }}>
                            {termsText}
                            {/* <Text>{' '}</Text>
                            <TouchableWithoutFeedback onPress={() => {

                            }}>
                                <Text style={{ color: '#e00000', textDecorationLine: 'underline', textDecorationColor: '#E00000' }}>Read More</Text>
                            </TouchableWithoutFeedback> */}
                        </Text>
                        <TouchableOpacity onPress={() => {
                            setSecondUserCheckedTandCPopup(!secondUserCheckedTandCPopup)
                        }} style={{ flexDirection: 'row', paddingHorizontal: 15, alignItems: 'center' }}>
                            <View style={{ width: 15, height: 15, borderWidth: 2, borderColor: '#E00000', padding: 2 }}>
                                {secondUserCheckedTandCPopup ? <View style={{ width: '100%', height: '100%', backgroundColor: '#E00000' }} /> : null}
                            </View>
                            <Text style={{ color: '#E00000', fontSize: 14, paddingLeft: 8 }}>Accept</Text>
                        </TouchableOpacity>
                        <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', paddingVertical: 10 }}>
                            <TouchableWithoutFeedback onPress={() => {
                                if (secondUserCheckedTandCPopup) {
                                    setSecondUserTandCPopup(false)
                                    //backHandler.remove()
                                    props.navigation.navigate('Gift', { obj: userInfo, user_type: userInfo.data.role == 2 ? 'mason' : 'te' })
                                } else {
                                    Toast.show({
                                        type: 'error',
                                        text2: 'Accept Terms & Condition',
                                        text2NumberOfLines: 2
                                    })
                                }
                            }}>
                                <View style={{ paddingHorizontal: 25, paddingVertical: 5, borderRadius: 5, backgroundColor: '#E00000' }}>
                                    <Text style={{ color: '#FFF', fontSize: 14 }}>Submit</Text>
                                </View>
                            </TouchableWithoutFeedback>
                        </View>
                    </View>
                    <Toast />
                </View>
            </ReactNativeModal>
        )
    }
    const renderOtherUserTandCPopup = () => {
        return (
            <ReactNativeModal
                isVisible={otherUserTandCPopup}
                style={{ margin: 0 }}
                customBackdrop={
                    <TouchableWithoutFeedback onPress={() => { setOtherUserTandCPopup(false) }}>
                        <View style={{ flex: 1, backgroundColor: 'black' }} />
                    </TouchableWithoutFeedback>
                }>
                <View style={{ width: '100%', height: '100%', alignItems: 'center', justifyContent: 'center' }}>
                    <View style={{ width: '85%', backgroundColor: '#FFF', borderRadius: 10 }}>
                        <View style={{ width: '100%', height: 50, backgroundColor: '#e00000', borderTopLeftRadius: 10, borderTopRightRadius: 10, alignItems: 'center', justifyContent: 'center' }}>
                            <Text style={{ color: '#FFF', fontWeight: '500', fontSize: 15 }}>Terms & Condition</Text>
                        </View>
                        <Text style={{ fontSize: 14, color: '#444', paddingHorizontal: 15, paddingVertical: 10 }}>
                            {termsText}
                            {/* <Text>{' '}</Text>
                            <TouchableWithoutFeedback onPress={() => {

                            }}>
                                <Text style={{ color: '#e00000', textDecorationLine: 'underline', textDecorationColor: '#E00000' }}>Read More</Text>
                            </TouchableWithoutFeedback> */}
                        </Text>
                        <TouchableOpacity onPress={() => {
                            setOtherUserCheckedTandCPopup(!otherUserCheckedTandCPopup)
                        }} style={{ flexDirection: 'row', paddingHorizontal: 15, alignItems: 'center' }}>
                            <View style={{ width: 15, height: 15, borderWidth: 2, borderColor: '#E00000', padding: 2 }}>
                                {otherUserCheckedTandCPopup ? <View style={{ width: '100%', height: '100%', backgroundColor: '#E00000' }} /> : null}
                            </View>
                            <Text style={{ color: '#E00000', fontSize: 14, paddingLeft: 8 }}>Accept</Text>
                        </TouchableOpacity>
                        <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', paddingVertical: 10 }}>
                            <TouchableWithoutFeedback onPress={() => {
                                if (otherUserCheckedTandCPopup) {
                                    setOtherUserTandCPopup(false)
                                    //backHandler.remove()
                                    props.navigation.navigate('Gift', { obj: userInfo, user_type: userInfo.data.role == 2 ? 'mason' : 'te' })
                                } else {
                                    Toast.show({
                                        type: 'error',
                                        text2: 'Accept Terms & Condition',
                                        text2NumberOfLines: 2
                                    })
                                }
                            }}>
                                <View style={{ paddingHorizontal: 25, paddingVertical: 5, borderRadius: 5, backgroundColor: '#E00000' }}>
                                    <Text style={{ color: '#FFF', fontSize: 14 }}>Submit</Text>
                                </View>
                            </TouchableWithoutFeedback>
                        </View>
                    </View>
                    <Toast />
                </View>
            </ReactNativeModal>
        )
    }
    const renderFirstUserRole = () => {
        return (
            <ScrollView style={{ width: '100%', paddingHorizontal: 30 }}
                showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                <View style={{ width: '100%', flexDirection: 'column' }}>
                    <View style={{ width: '100%', height: 120, flexDirection: 'row' }}>
                        <TouchableOpacity activeOpacity={0.8}
                            onPress={() => {
                                try {
                                    //backHandler.remove()
                                    props.navigation.navigate('MasonRegistration')
                                } catch (error) { }
                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.RegisterIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.MASON_REGISTRATION}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                            activeOpacity={0.8}
                            onPress={() => {
                                //backHandler.remove()
                                props.navigation.navigate('LiftingHistory')
                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.HistoryIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.LIFTING_HISTORY1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                            activeOpacity={0.8}
                            onPress={() => {
                                //backHandler.remove()
                                props.navigation.navigate('RewardDetails')
                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.PointIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.REWARD_POINTS}</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={{ width: '100%', height: 120, flexDirection: 'row' }}>
                        <TouchableOpacity
                            activeOpacity={0.8}
                            onPress={() => {
                                //backHandler.remove()
                                props.navigation.navigate('DashboardTe')
                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.DashBoradIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.DASHBOARD1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => {
                            //console.log('hello world')
                            props.navigation.navigate('Gift', { obj: userInfo, user_type: userInfo.data.role == 2 ? 'mason' : 'te' })
                            //backHandler.remove()
                            //setFirstUserTandCPopup(false)
                        }} activeOpacity={0.8} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.GiftIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.GIFT_CATALOGUE1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => {
                            //backHandler.remove()
                            props.navigation.navigate('AddLiftingTe')
                        }} activeOpacity={0.8} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.SalesIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.MASON_SALES_ENTRY1}</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={{ width: '100%', height: 120, flexDirection: 'row' }}>
                        <TouchableOpacity onPress={() => {
                            //backHandler.remove()
                            props.navigation.navigate('LiftingStatus')
                        }} activeOpacity={0.8} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.LiftingApprovalIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.LIFTING_APPROVAL1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => {
                            //backHandler.remove()
                            props.navigation.navigate('TeDealerLink')
                        }} activeOpacity={0.8} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.DelaerLinkRequestIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.DEALER_LINK_REQUEST1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => {
                            //backHandler.remove()
                            props.navigation.navigate('MassonList')
                        }} activeOpacity={0.8} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.DocUploadIcon} style={{ width: 45, height: 45, tintColor: '#ee1d23' }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.Contractor_Profile}</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </ScrollView>
        )
    }
    const renderSecondUserRole = () => {
        return (
            <ScrollView style={{ width: '100%', paddingHorizontal: 30 }}
                showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                <View style={{ width: '100%', flexDirection: 'column' }}>
                    <View style={{ width: '100%', height: 120, flexDirection: 'row' }}>
                        <TouchableOpacity
                            activeOpacity={0.8}
                            onPress={() => {
                                //backHandler.remove()
                                props.navigation.navigate('AddLefting')
                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.AddLiftingIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.ADD_LIFTING1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                            activeOpacity={0.8}
                            onPress={() => {
                                //backHandler.remove()
                                props.navigation.navigate('LiftingHistory')
                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.HistoryIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.LIFTING_HISTORY1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                            activeOpacity={0.8}
                            onPress={() => {
                                //backHandler.remove()
                                props.navigation.navigate('RewardDetails')
                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.PointIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.REWARD_POINTS}</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={{ width: '100%', height: 120, flexDirection: 'row' }}>
                        <TouchableOpacity
                            activeOpacity={0.8}
                            onPress={() => {
                                props.navigation.navigate('Gift', { obj: userInfo, user_type: userInfo.data.role == 2 ? 'mason' : 'te' })
                                //  setSecondUserTandCPopup(true)
                                //./setSecondUserTandCPopup(false)
                                //backHandler.remove()

                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.GiftIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.GIFT_CATALOGUE1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                            activeOpacity={0.8}
                            onPress={() => {
                                //backHandler.remove()
                                props.navigation.navigate('DealerRequest')
                            }}
                            style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.RequestDealerIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.REQUEST_A_DEALER1}</Text>
                        </TouchableOpacity>
                        <View style={{ flex: 1, height: '100%', }} />
                    </View>
                </View>
            </ScrollView>
        )
    }
    const renderOtherUserRole = () => {
        return (
            <ScrollView style={{ width: '100%', paddingHorizontal: 30 }}
                showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                <View style={{ width: '100%', flexDirection: 'column' }}>
                    <View style={{ width: '100%', height: 120, flexDirection: 'row' }}>
                        <TouchableOpacity onPress={() => {
                            //backHandler.remove()
                            props.navigation.navigate('AddLefting')
                        }}
                            activeOpacity={0.8} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.AddLiftingIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.ADD_LIFTING1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => {
                            //backHandler.remove()
                            props.navigation.navigate('LiftingHistory')
                        }}
                            activeOpacity={0.8} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.HistoryIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.LIFTING_HISTORY1}</Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => {
                            props.navigation.navigate('Gift', { obj: userInfo, user_type: userInfo.data.role == 2 ? 'mason' : 'te' })
                            //setOtherUserTandCPopup(true)
                            //setOtherUserTandCPopup(false)
                            //backHandler.remove()
                        }}
                            activeOpacity={0.8} style={{ flex: 1, height: '100%', borderRadius: 10, borderWidth: 1, borderColor: '#FFE5E7', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', backgroundColor: '#FFF' }}>
                            <Image source={ImagePath.GiftIcon} style={{ width: 45, height: 45 }} />
                            <View style={{ height: 5 }} />
                            <Text style={{ fontSize: 16, color: '#000', fontWeight: 600, textAlign: 'center' }}>{textValue.GIFT_CATALOGUE1}</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </ScrollView>
        )
    }
    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            {flagBanner ? <View style={{ height: Dimensions.get('window').height - 6, width: Dimensions.get('window').width - 1 }}>
                <Image source={{ uri: bannerLink }} style={{ height: '100%', width: '100%', resizeMode: 'stretch' }} />
                <TouchableOpacity onPress={() => setFlagBanner(false)} style={{ position: 'absolute', right: 3, top: 30 }}>
                    <View style={{ height: 30, width: 30, borderRadius: 15, justifyContent: 'center', alignItems: 'center', backgroundColor: 'grey' }}>
                        <Image style={{ height: 20, width: 20, tintColor: '#000000' }} source={Icons.rs_cancel} />
                    </View>
                </TouchableOpacity>
            </View> : null}

            <View style={{ flex: 1 }}>
                <View style={{ width: '100%', height: 100, borderBottomLeftRadius: 25, borderBottomRightRadius: 25, backgroundColor: '#EE1D23' }} />
                <View style={{ width: '100%', flex: 1, flexDirection: 'column-reverse' }}>
                    <Image source={ImagePath.Design1} style={{ width: '75%', resizeMode: 'contain', height: 100 }} />
                </View>
                <View style={{ width: '100%', height: '100%', position: 'absolute', flexDirection: 'column' }}>
                    {userInfo?.data ? <Toolbar notification={userInfo.data.unreadNotificationCount} obj={{ title: userInfo?.data?.role_name, icon: 'notification', language: 'show' }} openLanguagePopup={openLanguagePopup} /> :
                        <Toolbar obj={{ title: '', icon: 'notification', language: 'notshow' }} />}
                    <View style={styles._upperView}>
                        {userInfo ? <View style={styles._user_option_info_view}>
                            <View style={styles._user_info}>
                                <View style={styles._user_info._profile}>
                                    <Image style={styles._user_info._profile._img} source={userProfileInfo == null ? Icons.avatar : { uri: userProfileInfo }} />
                                    {/* + '?t=' + moment().valueOf() */}
                                </View>
                                <View style={{ marginLeft: 5, flex: 1 }}>
                                    {userInfo?.data?.role == 2 ? <View style={{ flexDirection: 'column' }}>
                                        <Text style={styles._user_info._eng_txt}>{convertForShowData(userInfo?.data?.name)}</Text>
                                        <Text style={styles._user_info._eng_txt_1}>{convertForShowData(userInfo?.data?.mason_category?.name) + '  [ ' + Number(convertForShowData(userInfo?.data?.points)).toFixed(2) + ' ]'}</Text>
                                    </View> : <Text style={styles._user_info._eng_txt}>{convertForShowData(userInfo?.data?.name)}</Text>}
                                    <View style={{ height: 5 }} />
                                    <View style={styles._user_info._mobile_section}>
                                        <Image style={styles._user_info._mobile_section._img} source={ImagePath.CallIconRed} />
                                        <View style={{ width: 5 }} />
                                        <Text style={styles._user_info._mobile_section._txt}>{convertForShowData('+91 ' + secure_mobile_no() + '****' + secure_mobile_last_two_digit())}</Text>
                                    </View>
                                </View>
                            </View>
                        </View> : null}
                    </View>
                    <View style={styles._slidder_img_box}>
                        {imageList ?
                            <View style={{ width: '100%', flexDirection: 'column' }}>
                                <SliderBox
                                    images={imageList}
                                    resizeMode={'stretch'}
                                    paginationBoxVerticalPadding={20}
                                    dotColor='transparent'
                                    inactiveDotColor='transparent'
                                    currentImageEmitter={(index) => setCurrentIndex(index)}
                                    resizeMethod={'resize'}
                                    ImageComponentStyle={{ borderRadius: 20, width: Dimensions.get('window').width - 60, marginTop: 0 }}
                                    imageLoadingColor='#ee1d23'
                                    sliderBoxHeight={180}
                                    autoplay={true}
                                    circleLoop
                                    autoplayInterval={3000} />

                                <View style={{ flexDirection: 'row', justifyContent: 'center', marginTop: 10, }}>
                                    {imageList.map((_, index) => (
                                        <View
                                            key={index}
                                            style={{ width: 10, height: 10, marginHorizontal: 5, backgroundColor: index === currentIndex ? '#0000' : '#E1E1E1', borderRadius: 10, borderWidth: 1, borderColor: index === currentIndex ? '#EE1D23' : '#0000' }}
                                        />
                                    ))}
                                </View>
                            </View>
                            : null}
                    </View>
                    <ScrollView showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                        <View style={{ width: '100%', flexDirection: 'column' }}>

                            <View style={{ height: 10 }} />
                            {userInfo?.data?.role == 1 ? renderFirstUserRole() : null}
                            {userInfo?.data?.role == 2 ? renderSecondUserRole() : null}
                            {userInfo?.data?.role == 3 || userInfo?.data?.role == 4 ? renderOtherUserRole() : null}
                            <View style={{ height: 20 }} />
                            {socialLink ? <View style={{ width: '100%', flexDirection: 'column' }}>
                                <Text style={{ width: '100%', textAlign: 'center', paddingHorizontal: 30, fontSize: 20, fontWeight: '600', color: '#000' }}>{textValue.Follow_Us}</Text>
                                <View style={{ height: 10 }} />
                                <View style={{ width: '100%', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                                    <TouchableOpacity activeOpacity={0.8} onPress={() => open_url(socialLink?.fb_link)} style={{ width: 45, height: 45 }}>
                                        <Image source={ImagePath.FaceBookIcon} style={{ width: 45, height: 45 }} />
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity activeOpacity={0.8} onPress={() => open_url(socialLink?.twitter_link)} style={{ width: 45, height: 45 }}>
                                        <Image source={ImagePath.YouTubeIcon} style={{ width: 45, height: 45 }} />
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity activeOpacity={0.8} onPress={() => open_url(socialLink?.web_link)} style={{ width: 45, height: 45 }}>
                                        <Image source={ImagePath.WebLinkIcon} style={{ width: 45, height: 45 }} />
                                    </TouchableOpacity>
                                    <View style={{ width: 10 }} />
                                    <TouchableOpacity activeOpacity={0.8} style={{ width: 45, height: 45 }} onPress={() => {
                                        Linking.openURL('whatsapp://send?text=&phone=' + socialLink?.whatsapp_no).then(res => { }).catch(err => {
                                            showToast('error', messageList.error, messageList.t36)
                                        })
                                    }}>
                                        <Image source={ImagePath.WhatsAppIcon} style={{ width: 45, height: 45 }} />
                                    </TouchableOpacity>
                                </View>
                            </View> : null}
                            <View style={{ height: 20 }} />
                        </View>
                    </ScrollView>
                </View>
            </View>
            {loading ? <Loader /> : null}
            {languagePopup ? <LanguagePicker {...props} saveLanguage={saveLanguage} /> : null}
            {isExitPopup ? <TouchableOpacity onPress={() => { }} style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0006', alignItems: 'center', justifyContent: 'center' }}>
                <View style={{ width: '80%', backgroundColor: '#fff', padding: 30, borderRadius: 20 }}>
                    <Text style={{ fontSize: 20, fontWeight: '600', color: '#000' }}>{messageList.exit_app}</Text>
                    <View style={{ height: 15 }} />
                    <Text style={{ fontSize: 14, color: '#5A5A5A' }}>{messageList.t5}</Text>
                    <View style={{ height: 30 }} />
                    <View style={{ width: '100%', flexDirection: 'row' }}>
                        <View style={{ flex: 1 }} />
                        <TouchableOpacity onPress={() => setExitPopup(false)} style={{ width: 100, height: 35, borderWidth: 1, borderRadius: 10, backgroundColor: '#FFF5F6', borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                            <Text style={{ color: '#5A5A5A', fontSize: 16, fontWeight: '500', textTransform: 'uppercase' }}>{textValue.Cancel}</Text>
                        </TouchableOpacity>
                        <View style={{ width: 10 }} />
                        <TouchableOpacity onPress={() => {
                            setExitPopup(false)
                            BackHandler.exitApp()
                        }} style={{ width: 100, height: 35, borderWidth: 1, borderRadius: 10, backgroundColor: '#EE1D23', borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                            <Text style={{ color: '#FFFFFF', fontSize: 16, fontWeight: '500', textTransform: 'uppercase' }}>{textValue.YES}</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </TouchableOpacity> : null}
            {/* {renderFirstUserTandCPopup()}
            {renderSecondUserTandCPopup()}
            {renderOtherUserTandCPopup()} */}
            <Modal animationType="fade" transparent={true} visible={acknowledgementVisibility} onRequestClose={() => setAcknowledgementVisibility(false)} >
                <View style={{ flex: 1, backgroundColor: '#0009', alignItems: 'center', justifyContent: 'center', }} >
                    <View style={{ width: '80%', backgroundColor: '#FFF', borderRadius: 10, paddingVertical: 25, paddingHorizontal: 20, alignItems: 'center', }} >
                        <Text style={{ fontSize: 18, color: '#000', fontWeight: '600', marginBottom: 10, textAlign: 'center', }} > Acknowledgement Required </Text>

                        <Text style={{ fontSize: 14, color: '#555', textAlign: 'center', marginBottom: 20, lineHeight: 20, }} > {ackMsg || 'You have a pending delivery acknowledgement. Please confirm.'} </Text>

                        <TouchableOpacity onPress={() => {
                                setAcknowledgementVisibility(false)
                                props?.navigation?.navigate('Order')
                            }}
                            style={{ backgroundColor: Colors.red, borderRadius: 5, paddingVertical: 10, paddingHorizontal: 25, }}
                        >
                            <Text style={{ color: '#FFF', fontSize: 15, fontWeight: '600' }}>Okay</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </Modal>
            <BirthdayPopup visible={showBirthdayPopup}
                userName={userInfo?.data?.name}
                birthData={birthData}
                onClose={() => { setShowBirthdayPopup(false), checkAndLogBirthday() }}
            />

            <DOBOverlay
                visible={showDOB}
                onClose={handleDOBClose}
                onConfirm={handleDOBConfirm}
            />

        </SafeView>
    )
}

export default Dashboard