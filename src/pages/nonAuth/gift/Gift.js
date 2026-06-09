import React, { useCallback, useEffect, useState } from 'react'
import { Text, View, Image, TouchableOpacity, FlatList, Platform, TextInput, Modal, StyleSheet } from 'react-native'
import { ScrollView } from 'react-native-virtualized-view'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Toast from 'react-native-toast-message'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import Gifts from '../../../components/gifts/Gifts'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData, convertForUploadData } from '../../../helper/constants/NumberConverter'
import ImagePath from '../../../image/ImagePath'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import Constants from '../../../helper/constants/Constants'
import { CommonActions } from '@react-navigation/native'


const Gift = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [gifts, setGifts] = useState([])
    const [itemId, setItemId] = useState('')
        const [showEmailPopup, setShowEmailPopup] = useState(false)
        const [phone, setPhone] = useState("")
        const [email, setEmail] = useState("")
        const [termsText, setTermsText] = useState('')
        const [visibleRedeem, setVisibleRedeem] = useState('')
        

    useEffect(() => {
        const focusListener = props.navigation.addListener('focus', () => {
            setLoading(true)
            getData()
        })
        return focusListener
    }, [props.navigation])
    useEffect(()=>{
        fetchLastOrderContactDetails()
        fetchTermsText()
    },[])

const fetchTermsText = async () => {
    try {
        const token = await AsyncStorage.getItem('access_token');

        const response = await fetch(
            Constants.base_url + Constants.terms_and_conditions,
            {
                method: 'GET',
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: 'application/json',
                },
            }
        );

        const result = await response.json();

        //console.log('TERMS FULL RESPONSE 👉', result.data);
        const terms =
            Array.isArray(result?.data)
                ? result?.data?.[0]?.royalty_terms_condition
                : result?.data?.royalty_terms_condition;

        setTermsText(terms || '');
        ////console.log('terms------', terms);

    } catch (error) {
        //console.log('fetchTermsText error =>', error);
    }
};

useEffect(() => {
    getSettings()
}, [])

const getSettings = async () => {
    try {
        const response = await getApiWithHeader(
            Constants.app_registration_link_visible + '?preferred_app_lang=' + selectedLanguage()
        )

        if (response.data?.status) {
            setVisibleRedeem(response?.data?.data?.[0]?.app_redeem_now_button ?? '')
        }
    } catch (e) {}
}



    const getData = async () => {
        getApiWithHeader(constants.get_gift_catalogues + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                setLoading(false)
                //console.log(constants.get_gift_catalogues + '?preferred_app_lang=' + selectedLanguage())

                if (response.data.status) {
                    setGifts(response.data.data)
                } else {
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else {
                        showToast('error', response?.data?.msg)
                    }
                }
            })
            .catch(err => {
                setLoading(false)
                showToast('error', messageList.t4)
            })
    }
    const fetchLastOrderContactDetails = async () => {
    const payload = {
        user_id: props?.route?.params?.obj?.data?.id,
    };

    //console.log('last-order-contact-details payload =>', payload);

    await postApiWithHeader(
        `${Constants.last_order_details}`,
        payload
    )
        .then(response => {
            //console.log('last-order-contact-details response =>', response?.data);

            if (response?.data?.status) {
                setEmail(response?.data?.data?.email ?? '');
                setPhone(response?.data?.data?.contact ?? '');
                setShowEmailPopup(true); 
            } else {
                showToast('error', response?.data?.message || messageList.t4);
            }
        })
        .catch(err => {
            //console.log('last-order-contact-details error =>', err);
            showToast('error', messageList.t4);
        });
    };



    const apply_redeemtion = async (data) => {
        var formData = new FormData()
        formData.append('user_id', data?.user_info?.id)
        formData.append('catalogue_id', data?.data?.id)
        formData.append('redeemed_point', data?.data?.point)
        formData.append('role', data?.user_info?.role)
        formData.append('address1', convertForUploadData(data?.address?.address1))
        formData.append('address2', convertForUploadData(data?.address?.address2))
        formData.append('city', convertForUploadData(data?.address?.city))
        formData.append('district', convertForUploadData(data?.address?.district))
        formData.append('state', convertForUploadData(data?.address?.state))
        formData.append('country', convertForUploadData(data?.address?.country))
        formData.append('pincode', convertForUploadData(data?.address?.pincode))
        email?.length > 0  && formData.append('email', convertForUploadData(email))
        formData.append('phone',convertForUploadData(phone))

        //console.log("abcd", phone)
        //console.log("abcd", email)
        // if (data?.address?.is_email_required) {
        //     formData.append('email', data?.address?.email)
        // }

        formData.append('preferred_app_lang', selectedLanguage())
        //console.log(formData)

        postApiWithHeader(constants.apply_redeemtion, formData)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    showToast('success', response.data.msg)
                            props.navigation.dispatch(
                                CommonActions.reset({
                                    index: 0,
                                    routes: [{ name: 'Dashboard' }],
                                })
                            )
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
            text2NumberOfLines: 2
        })
    }
    const renderGiftItem = useCallback(({ item, index }) => (
  <View key={item.id}>
    <Gifts
      obj={{
        status: item.id == itemId,
        data: item,
        index,
        type: props?.route?.params?.user_type,
        points: props?.route?.params?.obj?.data?.points,
        user_data: props?.route?.params?.obj?.data,
      }}
      sendData={modal_view}
      loaderOnOff={loaderOnOff}
      termsText={termsText}
      visibleRedeem={visibleRedeem}
    />
  </View>
), [itemId, termsText, phone, email]);

    function modal_view(data) {
        apply_redeemtion(data)
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

    const loaderOnOff = (id) => {
        setItemId(id)
        setLoading(!loading)
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
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{convertForShowData(textValue.GIFT_CATALOGUE)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {setTimeout(()=>{
                                props.navigation.goBack()
                            },500) }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', borderTopLeftRadius: 20, borderTopRightRadius: 20 }}>
                        <View style={{ position: 'absolute', borderTopLeftRadius: 20, borderTopRightRadius: 20, backgroundColor: '#FFF', height: 30, width: '100%' }}></View>
                        <View style={{ width: '100%', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', paddingVertical: 15, paddingHorizontal: 10 }}>
                            <Text style={{ fontSize: 22, color: '#000', fontWeight: '700' }}>{textValue.Check_Balance_Points}</Text>
                            <View style={{ height: 4 }} />
                            <Text style={{ fontSize: 12, color: '#5A5A5A' }}>{textValue.For_All_Your_Cement_Purchases}</Text>
                            <View style={{ height: 10 }} />
                        </View>
 <FlatList
                                data={gifts}
                                showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}
                                renderItem={renderGiftItem}
                                keyExtractor={item => item.id} />

                    </View>
                </View>
            </View>

            {loading ? <Loader /> : null}

                            <Modal
                            visible={showEmailPopup}
                            transparent
                            animationType="fade"
                        >
                            <View style={styles.overlay}>
                                <View style={styles.container}>
            
                                    {/* Header */}
                                    <View style={styles.header}>
                                        <Text style={styles.headerText}>Customer Details</Text>
                                        {/* <TouchableOpacity onPress={()=>{setShowEmailPopup(false)}}>
                                            <Text style={styles.close}>✕</Text>
                                        </TouchableOpacity> */}
                                    </View>
            
                                    {/* Alert Box */}
                                    <View style={styles.alertBox}>
                                        <Text style={styles.alertText}>
                                            Please check your contact information before redeeming points.
                                        </Text>
                                        <Text style={styles.updateText}>
                                            You can update it, if required.
                                        </Text>
                                    </View>
            
                                    {/* Email */}
                                    <Text style={styles.label}>{"✉️ Email " + "( optional )"}</Text>
                                    <TextInput
                                        style={styles.input}
                                        value={email}
                                        onChangeText={(text)=>{
                                            setEmail(text)
                                        }}
                                        keyboardType="email-address"
                                        editable={true}
                                    />
            
                                    {/* Phone */}
                                    <Text style={styles.label}>📞 Phone</Text>
                                    <TextInput
                                        style={styles.input}
                                        value={phone}
                                        onChangeText={(text)=>{
                                            setPhone(text)
                                        }}
                                        keyboardType="phone-pad"
                                        editable={true}
                                    />
            
                                    {/* Confirm Button */}
                                    <TouchableOpacity style={styles.button} onPress={()=>{
                                            if(phone.length === 10){
                                                setShowEmailPopup(false)

                                            }else{
                                                showToast("error", "Please enter a valid mobile number")
                                            }
                                    }}>
                                        <Text style={styles.buttonText}>Confirm</Text>
                                    </TouchableOpacity>
                                </View>
                            </View>
                            <Toast />
                        </Modal>
                     
                    
        </SafeView>
    )
}

export default Gift
const styles = StyleSheet.create({
      overlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  container: {
    width: '85%',
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 16,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  headerText: {
    fontSize: 16,
    fontWeight: '700',
    color: '#000',
  },
  close: {
    fontSize: 18,
    color: '#333',
  },
  alertBox: {
    backgroundColor: '#FFE5E5',
    padding: 12,
    borderRadius: 8,
    marginBottom: 14,
  },
  alertText: {
    color: '#D32F2F',
    fontSize: 13,
    fontWeight: '500',
  },
  updateText: {
    marginTop: 4,
    fontSize: 12,
    color: '#D32F2F',
  },
  hereText: {
    textDecorationLine: 'underline',
    fontWeight: '600',
  },
  label: {
    fontSize: 13,
    fontWeight: '600',
    marginBottom: 6,
    color: '#333',
  },
  input: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 6,
    padding: 10,
    marginBottom: 14,
    backgroundColor: '#F7F7F7',
    color: '#000',
  },
  button: {
    backgroundColor: '#E53935',
    paddingVertical: 12,
    borderRadius: 6,
    alignItems: 'center',
    marginTop: 4,
  },
  buttonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '700',
  },
})