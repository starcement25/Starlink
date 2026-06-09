import React, { useState, useEffect } from 'react'
import { View, Text, TouchableOpacity, Image, FlatList, Modal, ActivityIndicator } from 'react-native'
import Icon2 from 'react-native-vector-icons/AntDesign'
import Icon3 from 'react-native-vector-icons/Ionicons'
import styles from './GiftsStyle'
import Address from '../../components/address/Address'
import useTextValue from '../../helper/constants/useTextValue'
import Icons from '../../helper/image/ImageList'
import { getApiWithHeader } from '../../helper/http/Api'
import Constants from '../../helper/constants/Constants'
import selectedLanguage from '../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../helper/constants/NumberConverter'

let color_code = [
    { name: 'BRONZE', color: '#b87f0d' },
    { name: 'BRONZE +', color: '#b06304' },
    { name: 'SILVER', color: '#e8e8e8' },
    { name: 'SILVER +', color: '#cccccc' },
    { name: 'GOLD', color: '#ffdf00' },
    { name: 'GOLD +', color: '#c9ba54' },
    { name: 'DIAMOND', color: '#b8d8e7' },
    { name: 'DIAMOND +', color: '#9ac5db' },
    { name: 'TITANIUM', color: '#e6dca6' },
    { name: 'TITANIUM +', color: '#deca93' },
    { name: 'PLATINUM', color: '#faf9f6' },
    { name: 'PLATINUM+', color: '#f1f0ed' },
    { name: 'KOHINOOR', color: '#b4b1e2' },
    { name: 'KOHINOOR+', color: '#827ec0' },
    { name: 'SUPERSTAR', color: '#fc2279' },
    { name: 'MEMBER', color: '#2db48e' }
]

export default function Gifts(props) {
    const textValue = useTextValue()

    const [isCollapsed, setIsCollapsed] = useState(false)
    const [modalVisible, setModalVisible] = useState(false)
    const [modalType, setModalType] = useState('')
    const [modalMsg, setModalMsg] = useState('')
    const [modalTitle, setModalTitle] = useState('')
    const [redeemableItem, setRedeemableItem] = useState('')
    const [addressModal, setAddressModal] = useState(false)
    const [listItem, setListItem] = useState(false)
    const [abcd, setABCD] = useState(false)

    useEffect(() => {
        setIsCollapsed(props?.obj?.status)
    }, [props.obj.status])

    function responce_address(data) {
        if (data.address == 'close') {
            setAddressModal(false)
        } else {
            props.sendData({ data: redeemableItem, user_info: props?.obj?.user_data, address: data.address, status: data.status })
        }
    }

    const _get_color = (name) => {
        for (let i = 0; i < color_code.length ;i++) {
            if (name == color_code[i].name) {
                return color_code[i].color
            }
        }
    }

    const requestForItemList = (id) => {
         setABCD(true)
        getApiWithHeader(Constants.get_gifts_by_catalogue + id + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                // props.loaderOnOff(id)
                if (response.data.status) {
                     setListItem(response.data.data)
                    setABCD(false)
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
                // props.loaderOnOff(id)
                setABCD(false)
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

    const hiFunction = () => {
        setIsCollapsed(!isCollapsed)
        requestForItemList(props?.obj?.data?.id)
    }
    return (
        <View style={styles.container}>
            <TouchableOpacity activeOpacity={0.8} onPress={() => {
                hiFunction()
            }}
                style={[isCollapsed ? styles.before : styles.after, { backgroundColor: _get_color(props?.obj?.data?.keyword) }]}>
                <Text style={styles._title}>{convertForShowData(props?.obj?.data?.name)}</Text>
                <View style={[styles._img_view, { backgroundColor: _get_color(props?.obj?.data?.keyword) }]}>
                    <Image source={Icons.arrow_icon} style={[styles._arrow_icon, { transform: [{ rotate: isCollapsed ? '180deg' : '0deg' }] }]} />
                </View>
            </TouchableOpacity>

            {isCollapsed ? <View style={styles._collapsible_view}>
                <FlatList
                    data={listItem}
                    renderItem={({ item, index }) => (
                        <>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                onPress={() => {
                                    if (props?.obj?.type == 'mason' && Number(props?.obj?.points) >= Number(item?.point)) {
                                        setRedeemableItem(item)
                                        setModalType('confirm')
                                        setModalVisible(true)
                                    }
                                }}
                                style={styles._grid_view}>
                                <View style={styles._grid_view._view}>
                                    <TouchableOpacity
                                        activeOpacity={0.8}
                                        style={styles._grid_view._view._info_icon_view}
                                        onPress={() => {
                                            setModalMsg(item.name + '\n\n' + item.description)
                                            setModalTitle(item.name)
                                            setModalType('info')
                                            setModalVisible(true)
                                        }}
                                    >
                                        <Icon3 name='information-circle' size={25} color='gray' />
                                    </TouchableOpacity>

                                    <View style={styles._grid_view._view._img_view}>
                                        <Image style={styles._grid_view._view._img_view._img} source={{ uri: item?.image }} />
                                    </View>
                                    <View style={styles._grid_view._view._txt_view}>
                                        <Text numberOfLines={2} style={styles._grid_view._view._txt_view._place_txt}>{convertForShowData(item?.name)}</Text>
                                    </View>
                                    <View style={styles._grid_view._view._point_view}>
                                        <Text style={styles._grid_view._view._point_view._point_txt}>{convertForShowData(item?.point + ' ' + textValue.Points)}</Text>
                                    </View>
                                </View>

                                {props?.obj?.type == 'mason' ? <View style={Number(props?.obj?.points) < Number(item?.point) ? styles._grid_view._high_light_view : null}>
                                    {Number(props?.obj?.points) < Number(item?.point) ? <Image style={styles._grid_view._high_light_view._img} source={Icons._lock_icon} /> : null}
                                </View> : null}

                            </TouchableOpacity>

                            {listItem.length == 1 ? <View style={styles._grid_view}>

                            </View> : null}
                        </>
                    )}
                    keyExtractor={item => item.id}
                    numColumns={2}
                />
            </View> : null}
            {abcd ? <View style={{
                width: '95%', height: 100, justifyContent: 'center',
                alignItems: 'center',
            }}>
                <ActivityIndicator size='large' color='#ee1d23' />
            </View> : null}

            {/* <Collapsible collapsed={isCollapsed} style={styles._collapsible_view}>
                

            </Collapsible> */}

            <Modal
                animationType='fade'
                transparent={true}
                visible={modalVisible}
                onRequestClose={(res) => {
                    setModalVisible(!modalVisible)
                }}
            >
                <View style={styles._modal}>
                    {modalType == 'info' && (
                        <View style={styles._modal._info_view}>
                            <Text style={styles._modal._info_view._txt}>{convertForShowData(modalMsg)}</Text>
                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={styles._modal._info_view._btn}
                                onPress={() => {
                                    setModalType('')
                                    setModalVisible(false)
                                }}
                            >
                                <Icon2 name='closecircle' size={25} color='#ee1d23' />
                            </TouchableOpacity>
                        </View>
                    )}
                    {modalType == 'confirm' && (
                        <View style={styles._modal._confirm_view}>
                            <View style={[styles._grid_view._view, { borderColor: '#00000000', width: '100%' }]}>

                                <View style={styles._grid_view._view._img_view}>
                                    <Image style={styles._grid_view._view._img_view._img} source={{ uri: redeemableItem?.image }} />
                                </View>

                                <View style={[styles._grid_view._view._txt_view, { height: 'auto', padding: 0 }]}>
                                    <Text style={styles._grid_view._view._txt_view._place_txt}>{convertForShowData(redeemableItem?.name)}</Text>
                                </View>

                            </View>
                            <Text style={styles._modal._confirm_view._txt}>{convertForShowData(textValue.Are_you_sure_to_redeemed_this_product)}</Text>
                            <View style={styles._modal._confirm_view._btn_section}>
                                <TouchableOpacity
                                    activeOpacity={0.8}
                                    style={styles._modal._confirm_view._btn_section._btn}
                                    onPress={() => {
                                        setModalType('')
                                        setModalVisible(false)
                                        setAddressModal(true)
                                    }}
                                >
                                    <Text style={styles._modal._confirm_view._btn_section._btn._txt}>{convertForShowData(textValue.Redeem)}</Text>
                                </TouchableOpacity>
                            </View>

                            <TouchableOpacity
                                activeOpacity={0.8}
                                style={styles._modal._confirm_view._btn_section._close_btn}
                                onPress={() => {
                                    setModalType('')
                                    setModalVisible(false)
                                }}
                            >
                                <Icon2 name='closecircle' size={25} color='#ee1d23' />
                            </TouchableOpacity>

                        </View>
                    )}
                </View>

            </Modal>

            {addressModal ? <Address obj={{ user_info: props?.obj?.user_data }} sendData={responce_address}></Address> : null}

        </View>
    )
}