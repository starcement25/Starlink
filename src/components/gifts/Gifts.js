import React, { useState, useEffect } from 'react'
import { View, Text, TouchableOpacity, Image, FlatList, Modal, ActivityIndicator, StyleSheet, TouchableWithoutFeedback, ScrollView, useWindowDimensions, TextInput } from 'react-native'
import Icon2 from 'react-native-vector-icons/AntDesign'
import Toast from 'react-native-toast-message'

import Address from '../../components/address/Address' // your new modal component (expects visible + onClose)
import useTextValue from '../../helper/constants/useTextValue'
import Icons from '../../helper/image/ImageList'
import { getApiWithHeader } from '../../helper/http/Api'
import Constants from '../../helper/constants/Constants'
import selectedLanguage from '../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../helper/constants/NumberConverter'
import ImagePath from '../../image/ImagePath'
import useMessageList from '../../helper/constants/useMessageList'
import ReactNativeModal from 'react-native-modal'
import AsyncStorage from '@react-native-async-storage/async-storage'
import RenderHTML from 'react-native-render-html'

let color_code = [
    { name: 'BRONZE', color: '#b87f0df0' },
    { name: 'BRONZE +', color: '#b06304f0' },
    { name: 'SILVER', color: '#e8e8e8f0' },
    { name: 'SILVER +', color: '#ccccccf0' },
    { name: 'GOLD', color: '#ffdf00f0' },
    { name: 'GOLD +', color: '#c9ba54f0' },
    { name: 'DIAMOND', color: '#b8d8e7f0' },
    { name: 'DIAMOND +', color: '#9ac5dbf0' },
    { name: 'TITANIUM', color: '#e6dca6f0' },
    { name: 'TITANIUM +', color: '#deca93f0' },
    { name: 'PLATINUM', color: '#faf9f6f0' },
    { name: 'PLATINUM+', color: '#f1f0edf0' },
    { name: 'KOHINOOR', color: '#b4b1e2f0' },
    { name: 'KOHINOOR+', color: '#827ec0f0' },
    { name: 'SUPERSTAR', color: '#fc2279f0' },
    { name: 'MEMBER', color: '#2db48ef0' }
]

export default function Gifts(props) {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [isCollapsed, setIsCollapsed] = useState(false)
    const [modalVisible, setModalVisible] = useState(false)
    const [modalType, setModalType] = useState('')
    const [modalMsg, setModalMsg] = useState('')
    const [redeemableItem, setRedeemableItem] = useState({}) // object not string
    const [addressModal, setAddressModal] = useState(false)
    const [listItem, setListItem] = useState([]) // array
    const [abcd, setABCD] = useState(false)
    const [firstUserCheckedTandCPopup, setFirstUserCheckedTandCPopup] = useState(false)
    const [tncPopup, setTncPopup] = useState(false)
    // const [termsText, setTermsText] = useState('')
    const [visibleRedeem, setVisibleReedem] = useState(props?.visibleRedeem)
    const [insufficientVisible, setInsufficientVisible] = useState(false)
    


    //  (item?.tds_percentage != null || item?.tds_point != null || item?.total_point != null) && (
//                                                             <View style={{ paddingHorizontal: 4, paddingBottom: 4, paddingTop: 2 }}>
//                                                                 <View
//                                                                     style={{
//                                                                         flexDirection: "row",
//                                                                         justifyContent: "center",
//                                                                         alignItems: "center",
//                                                                         backgroundColor: "#FFF5F5",
//                                                                         borderRadius: 8,
//                                                                         paddingHorizontal: 4,
//                                                                         paddingVertical: 4,
//                                                                         height: 50,
//                                                                     }}
//                                                                 >
//                                                                     {/* POINTS COLUMN */}
//                                                                     <View style={{ flex: 0.4, alignItems: "center" }}>
//                                                                         <Text style={{ fontSize: 9, color: "#8A8A8A" }}>Points</Text>
//                                                                         <Text style={{ fontSize: 10, fontWeight: "700", color: "#444" }}>
//                                                                             {convertForShowData(item?.point != null ? String(item?.point) : "—")}
//                                                                         </Text>
//                                                                     </View>

//                                                                     {/* TDS COLUMN WITH CONCATED FORMAT */}
//                                                                     <View style={{ flex: 0.6, alignItems: "center" }}>
//                                                                         <Text style={{ fontSize: 9, color: "#8A8A8A" }}>TDS</Text>
//                                                                         <Text style={{ fontSize: 10, fontWeight: "700", color: "#444" }}>
//                                                                             {convertForShowData(
//                                                                                 item?.tds_point != null
//                                                                                     ? `${item?.tds_point} (${item?.tds_percentage ?? 0}%)`
//                                                                                     : "—"
//                                                                             )}
//                                                                         </Text>
//                                                                     </View>
//                                                                 </View>
//                                                             </View>
//                                                         )

    useEffect(() => {
        setIsCollapsed(!!props?.obj?.status)
    }, [props?.obj?.status])

    useEffect(() => {
        //getSettings()
        //getTermsText()
    }, [])

    const getSettings = async () => {
        getApiWithHeader(Constants.app_registration_link_visible + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data?.status) {
                    setVisibleReedem(response?.data?.data?.[0]?.app_redeem_now_button ?? '')
                }
            })
            .catch(err => { })
    }

    function responce_address(data) {
        setAddressModal(false)

        if (data && data.address && data.address !== 'close') {
            props.sendData({ data: redeemableItem, user_info: props?.obj?.user_data, address: data.address, status: data.status })
            setModalVisible(false)
            setModalType('')
            setRedeemableItem({})
        } else {
            // user cancelled
        }
    }

    const _get_color = (name) => {
        for (let i = 0; i < color_code.length; i++) {
            if (name == color_code[i].name) {
                return color_code[i].color
            }
        }
        return '#ffffff00'
    }
    // const getTermsText = async () => {
    //     const token = await AsyncStorage.getItem('access_token');
    //     const myHeaders = new Headers();
    //     myHeaders.append("Authorization", `Bearer ${token}`);
    //     myHeaders.append("Accept", "application/json")

    //     const requestOptions = {
    //         method: "GET",
    //         headers: myHeaders,
    //         redirect: "follow"
    //     };
    //     //console.log("terms------>", Constants.base_url + Constants.terms_and_conditions)
    //     await fetch(Constants.base_url + Constants.terms_and_conditions, requestOptions)
    //         .then((response) => { return response.json() })
    //         .then((result) => {
    //             //console.log("terms------>", result)
    //             setTermsText(result?.data[0]?.royalty_terms_condition)
    //         })
    //         .catch((error) => console.error(error));
    // }
    const renderTandCPopup = () => {
        const { width } = useWindowDimensions()

        const htmlSource = {
            html: props.termsText
                ? `<div>${props.termsText}</div>`
                : `<p>No Terms & Conditions available.</p>`,
        }

        return (
            <ReactNativeModal
                isVisible={tncPopup}
                style={{ margin: 0 }}
                customBackdrop={
                    <TouchableWithoutFeedback onPress={() => setTncPopup(false)}>
                        <View style={{ flex: 1, backgroundColor: 'black' }} />
                    </TouchableWithoutFeedback>
                }
            >
                <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center' }}>
                    <View style={{ width: '85%', backgroundColor: '#FFF', borderRadius: 10, maxHeight: '80%', }} >
                        {/* HEADER */}
                        <View style={{ width: '100%', height: 50, backgroundColor: '#e00000', borderTopLeftRadius: 10, borderTopRightRadius: 10, alignItems: 'center', justifyContent: 'center', }} >
                            <Text style={{ color: '#FFF', fontWeight: '500', fontSize: 15 }}>
                                Terms & Conditions
                            </Text>
                        </View>

                        {/* SCROLLABLE CONTENT */}
                        <View style={{ maxHeight: '65%' }}>
                            <ScrollView contentContainerStyle={{ paddingHorizontal: 15, paddingVertical: 10, }} >
                                <RenderHTML contentWidth={width * 0.85} source={htmlSource} tagsStyles={{
                                        p: { fontSize: 14, color: '#444', lineHeight: 20, marginBottom: 6, },
                                        li: { fontSize: 14, color: '#444', lineHeight: 20, },
                                        strong: { fontWeight: '600', },
                                        ul: { paddingLeft: 15, },
                                    }}
                                />
                            </ScrollView>
                        </View>

                        {/* ACCEPT CHECKBOX */}
                        <TouchableOpacity
                            onPress={() => setFirstUserCheckedTandCPopup(!firstUserCheckedTandCPopup)}
                            style={{ flexDirection: 'row', paddingHorizontal: 15, alignItems: 'center', paddingTop: 10, }}
                        >
                            <View style={{ width: 18, height: 18, borderWidth: 2, padding: 2, borderColor: '#E00000', justifyContent: 'center', alignItems: 'center', }} >
                                {firstUserCheckedTandCPopup && (
                                    <View style={{ width: '100%', height: '100%', backgroundColor: '#E00000' }} />
                                )}
                            </View>
                            <Text style={{ color: '#E00000', fontSize: 14, paddingLeft: 8 }}> Accept </Text>
                        </TouchableOpacity>

                        {/* SUBMIT BUTTON */}
                        <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', paddingVertical: 12, }} >
                            <TouchableOpacity onPress={() => {
                                    if (firstUserCheckedTandCPopup) {
                                        setModalType('confirm')
                                        setModalVisible(true)
                                    } else {
                                        Toast.show({ type: 'error', text2: 'Accept Terms & Condition', text2NumberOfLines: 2, })
                                    }
                                    setTncPopup(false)
                                }}
                                style={{ paddingHorizontal: 25, paddingVertical: 8, borderRadius: 5, backgroundColor: '#E00000', }}
                            >
                                <Text style={{ color: '#FFF', fontSize: 15 }}>Submit</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
                <Toast />
            </ReactNativeModal>
        )
    }



    const requestForItemList = (id) => {
        setABCD(true)
        getApiWithHeader(Constants.get_gifts_by_catalogue + id + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                if (response.data?.status) {
                    //console.log("item-----",response.data.data )
                    setListItem(response.data.data ?? [])
                    setABCD(false)
                } else {
                    if (response?.data?.status_code == 401) {
                        showToast('error', response?.data?.message)
                        _logout()
                    } else {
                        showToast('error', response?.data?.msg)
                    }
                    setABCD(false)
                }
            })
            .catch(err => {
                setABCD(false)
                showToast('error', messageList.t4)
            })
    }

    const showToast = (type, msg) => {
        Toast.show({ type: type, text2: msg, text2NumberOfLines: 2 })
    }

    const hiFunction = () => {
        if (isCollapsed) {
            setIsCollapsed(!isCollapsed)
        } else {
            setIsCollapsed(!isCollapsed)
            requestForItemList(props?.obj?.data?.id)
        }
    }

    
    return (
        <View style={{ width: '100%', minHeight: 100, alignItems: 'center', justifyContent: 'center', marginTop: props?.obj?.index == 0 ? 0 : -35 }}>
            <View style={{ width: '100%', minHeight: 100, flexDirection: 'column', borderRadius: 20, borderWidth: 1, borderColor: '#FFF', backgroundColor: _get_color(props?.obj?.data?.keyword) }}>
                <TouchableOpacity activeOpacity={0.8} onPress={() => { hiFunction() }}
                    style={{ height: 50, width: '90%', justifyContent: 'center', borderTopLeftRadius: 5, borderTopRightRadius: 5, position: 'relative', marginTop: 10, paddingLeft: 16 }}>
                    <Text style={{ color: '#000000', fontWeight: '600' }}> {convertForShowData(props?.obj?.data?.name)} </Text>
                    <View style={{ height: '100%', width: 30, position: 'absolute', right: 0, justifyContent: 'center', alignItems: 'center', borderRadius: 5 }}>
                        <View style={{ width: 30, height: 30, backgroundColor: '#FFFFFF90', borderWidth: 1, borderColor: '#FFF', borderRadius: 15, alignItems: 'center', justifyContent: 'center' }}>
                            <Image source={isCollapsed ? ImagePath.RemoveIcon : ImagePath.AddIcon} style={{ width: 10, height: 10, resizeMode: 'contain' }} />
                        </View>
                    </View>
                </TouchableOpacity>

                {isCollapsed ? (
                    <View style={{ width: '100%', flexDirection: 'column', alignItems: 'center', justifyContent: 'center' }}>
                        <View style={{ backgroundColor: '#fff0', width: '95%' }}>
                            {!abcd && listItem.length == 0 ? (
                                <>
                                    <View style={{ width: '100%', height: 100, alignItems: 'center', justifyContent: 'center' }}>
                                        <Text style={{ color: '#FFF', fontSize: 16 }}> {textValue.Gift_not_available} </Text>
                                    </View>
                                    <View style={{ height: 35 }} />
                                </>
                            ) : (
                                <>
                                    <FlatList
                                        data={listItem}
                                        showsHorizontalScrollIndicator={false}
                                        showsVerticalScrollIndicator={false}
                                        renderItem={({ item, index }) => (
                                            <>
                                                <TouchableOpacity
                                                    activeOpacity={1}
                                                    style={{ width: '50%', height: 280, justifyContent: 'center', alignItems: 'center', overflow: 'hidden' }}
                                                    onPress={() => {
                                                        if (
                                                            props?.obj?.type == 'mason' &&
                                                            Number(props?.obj?.points) >= Number(item?.total_point)
                                                        ) {
                                                            setRedeemableItem(item)
                                                            setTncPopup(true)
                                                        }else if(props?.obj?.type == 'mason' && Number(props?.obj?.points) < Number(item?.total_point)) {
                                                            setInsufficientVisible(true)
                                                        }
                                                    }}>
                                                    <View style={{ width: '90%', height: 240, backgroundColor: '#FFFFFF', borderColor: '#C8C8C8', borderWidth: 1, borderRadius: 15, overflow: 'hidden', position: 'relative' }}>
                                                        <View style={{ position: 'absolute', top: 4, height: 30, width: 30, zIndex: 4, alignItems: 'center' }}>
                                                            <Image source={ImagePath.GiftSticker} style={{ width: 30, height: 30, resizeMode: 'contain' }} />
                                                        </View>

                                                        {/* IMAGE */}
                                                        <View style={{ width: '100%', height: 160, justifyContent: 'center', alignItems: 'center', padding: '5%' }}>
                                                            <Image style={{ height: '100%', width: '100%', resizeMode: 'contain' }} source={{ uri: item?.image }} />
                                                        </View>

                                                        {/* TITLE */}
                                                        <View style={{ alignItems: 'center', padding: 4 }}>
                                                            <Text numberOfLines={2} style={{ fontSize: 10, fontWeight: '600', color: '#5A5A5A' }}> {convertForShowData(item?.name)} </Text>
                                                        </View>

                                                        {/* BOTTOM POINTS BAR (UNCHANGED) */}
                                                        <View style={{ flex: 1, width: '100%', alignItems: 'center', justifyContent: 'center', backgroundColor: '#EE1D23', borderBottomLeftRadius: 15, borderBottomRightRadius: 15 }}>
                                                            <Text style={{ fontSize: 15, color: '#FFF', fontWeight: '800' }}> {convertForShowData( item?.point + ' ' + textValue.Points )} </Text>
                                                        </View>
                                                    </View>

                                                    {/* LOCK OVERLAY */}
                                                    {props?.obj?.type == 'mason' ? (
                                                        <View style={ Number(props?.obj?.points) < Number(item?.total_point)
                                                                    ? { width: '90%', height: 240, backgroundColor: '#00000050', position: 'absolute', borderRadius: 15, justifyContent: 'center', alignItems: 'center' }
                                                                    : null
                                                            }>
                                                            {Number(props?.obj?.points) < Number(item?.total_point) ? (
                                                                <Image style={{ height: '25%', width: '25%', tintColor: '#696A6B' }} source={Icons._lock_icon} /> ) : null}
                                                        </View>
                                                    ) : null}
                                                </TouchableOpacity>

                                                {listItem.length == 1 ? (
                                                    <View style={{ width: '50%', height: 250, justifyContent: 'center', alignItems: 'center', overflow: 'hidden' }} />
                                                ) : null}
                                            </>
                                        )}
                                        keyExtractor={(item) => String(item?.id ?? Math.random())}
                                        numColumns={2}
                                    />
                                    <View style={{ height: 35 }} />
                                </>
                            )}
                        </View>
                    </View>
                ) : null}
                {renderTandCPopup()}
                {abcd ? (
                    <View style={{ width: '95%', height: 100, justifyContent: 'center', alignItems: 'center' }}>
                        <ActivityIndicator size='large' color='#ee1d23' />
                    </View>
                ) : null}
                <View style={{ width: isCollapsed ? 50 : 35 }} />
            </View>

            {/* Confirm / Info Modal */}
            <Modal animationType='fade' transparent={true} visible={modalVisible}
                onRequestClose={() => {
                    setModalVisible(false)
                    setModalType('')
                }}>
                <View style={{ height: '100%', width: '100%', backgroundColor: '#00000095', justifyContent: 'center', alignItems: 'center' }}>
                    {modalType == 'info' && (
                        <View style={{ backgroundColor: '#fff', borderRadius: 10, padding: 16, alignItems: 'center', width: '80%', position: 'relative' }}>
                            <Text style={{ fontSize: 12, color: '#000' }}>
                                {convertForShowData(modalMsg)}
                            </Text>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={{ height: 35, width: 35, justifyContent: 'center', alignItems: 'center', position: 'absolute', right: 0, top: 0, backgroundColor: '#fff', borderRadius: 5 }}
                                onPress={() => {
                                    setModalType('')
                                    setModalVisible(false)
                                }}>
                                <Icon2 name='closecircle' size={25} color='#ee1d23' />
                            </TouchableOpacity>
                        </View>
                    )}

                    {modalType == 'confirm' && (
                        <View
                            style={{ backgroundColor: '#fff', borderRadius: 5, padding: 16, alignItems: 'center', width: '80%', position: 'relative' }}>
                            <View style={{ width: '90%', minHeight: 190, backgroundColor: '#FFFFFF', borderColor: '#C8C8C8', borderWidth: 1, borderRadius: 15, overflow: 'hidden', position: 'relative', borderColor: '#00000000', width: '100%' }}>
                                <View style={{ width: '100%', height: 140, justifyContent: 'center', alignItems: 'center', padding: '5%',}}>
                                    <Image style={{ height: '100%', width: '100%', resizeMode: 'contain' }} source={{ uri: redeemableItem?.image }} />
                                </View>
                                <View style={{ alignItems: 'center', padding: 4 }}>
                                    <Text style={{ fontSize: 10, fontWeight: '600', color: '#EE1D23' }}> {convertForShowData(redeemableItem?.name)} </Text>
                                </View>
                            </View>

                            {visibleRedeem !== '0' ? (
                                <Text style={{ fontSize: 14, fontWeight: '700', marginTop: 8 }}> {convertForShowData( textValue.Are_you_sure_to_redeemed_this_product )} </Text>
                            ) : null}

                            {visibleRedeem !== '0' ? (
                                <View style={{ width: '100%', flexDirection: 'row', justifyContent: 'space-evenly' }}>
                                    <TouchableOpacity
                                        activeOpacity={0.8}
                                        style={{ height: 40, width: '90%', backgroundColor: '#ee1d23', justifyContent: 'center', alignItems: 'center', marginTop: 16, borderRadius: 5 }}
                                        onPress={() => {
                                            setModalType('')
                                            setModalVisible(false)
                                            setAddressModal(true)
                                        }}>
                                        <Text style={{ color: '#fff', fontWeight: '600', fontSize: 14 }}> {convertForShowData(textValue.Redeem)} </Text>
                                    </TouchableOpacity>
                                </View>
                            ) : null}

                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={{ height: 35, width: 35, justifyContent: 'center', alignItems: 'center', position: 'absolute', right: 0, top: 0, backgroundColor: '#fff', borderRadius: 5 }}
                                onPress={() => {
                                    setModalType('')
                                    setModalVisible(false)
                                }}>
                                <Icon2 name='closecircle' size={25} color='#ee1d23' />
                            </TouchableOpacity>
                        </View>
                    )}
                </View>
            </Modal>

            {/* Parent-controlled Address modal */}
            <Address visible={addressModal} obj={{ user_info: props?.obj?.user_data }} redeemableItem={redeemableItem}
                onClose={(result) => {
                    responce_address(result)
                }}
            />
            <Modal animationType="fade" transparent={true} visible={insufficientVisible}>
                <View style={styles.overlay}>
                    <View style={styles.popup}>
                        <Text style={styles.title}>Insufficient Balance</Text>

                        <Text style={styles.message}>
                            Your account does not have sufficient balance including TDS to place the order.
                        </Text>

                        <TouchableOpacity
                            style={styles.backButton}
                            onPress={() => {
                                setInsufficientVisible(false);
                            }}>
                            <Text style={styles.backText}>Back</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </Modal>
        </View>
    )
}
const styles = StyleSheet.create({
  overlay: { flex: 1, backgroundColor: '#00000090', justifyContent: 'center', alignItems: 'center', },
  popup: { width: '80%', backgroundColor: 'white', borderRadius: 16, overflow: 'hidden', alignItems: 'center', paddingBottom: 16, elevation: 10, shadowOpacity: 0.3, },
  title: { fontSize: 18, fontWeight: '800', color: '#FFF', width: '100%', paddingVertical: 10, paddingHorizontal: 12, backgroundColor: '#EE1D23', textAlign: 'center', },
  message: { fontSize: 14, fontWeight: '600', color: '#000', textAlign: 'center', lineHeight: 20, marginTop: 16, marginHorizontal: 16, marginBottom: 20, },
  backButton: { minWidth: 120, paddingVertical: 10, paddingHorizontal: 24, borderRadius: 10, backgroundColor: '#EE1D23', alignItems: 'center', justifyContent: 'center', marginBottom: 8, },
  backText: { fontSize: 16, fontWeight: '700', color: '#FFF', },
});
