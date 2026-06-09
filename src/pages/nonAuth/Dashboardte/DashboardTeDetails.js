import React, { useCallback, useRef, useState } from 'react'
import { Text, View, Image, TouchableOpacity, FlatList, ActivityIndicator, Platform } from 'react-native'
import Toast from 'react-native-toast-message'
import useTextValue from '../../../helper/constants/useTextValue'
import Icons from '../../../helper/image/ImageList'
import { getApiWithHeader } from '../../../helper/http/Api'
import useMessageList from '../../../helper/constants/useMessageList'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

const DashboardTeDetails = (props) => {
    const textValue = useTextValue()
    const messageList = useMessageList()

    const [loading, setLoading] = useState(true)
    const [category, setCategory] = useState(props.route.params.category)
    const [details, setDetails] = useState([])

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setLoading(true)
            requestForDetails(1, [])
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    const requestForDetails = (page_value, value) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        setLoading(true)
        getApiWithHeader(props.route.params.url + '&page=' + page_value)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
                    var a = value
                    a = [...a, ...response.data.data]
                    setDetails(a)
                    requestForDetails(page_value + 1, a)
                } else {
                    if (page_value == 1) { showToast('error', textValue.No_Data_Found) }
                    setLoading(false)
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

    const renderItem = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Date)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.date)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Dealer)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.dealer)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Dealer_Code)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.dealer_code)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_mobile)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_mobile)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_branch)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_branch)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Code)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_code)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_phone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Zone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.zone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Product_name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.product_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Product_quantity)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.product_quantity)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Points)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.point)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Status)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.status)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Verified_by)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.verified_by)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderItem_one = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Address_1)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.address1 ? convertForShowData(item.address1) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Address_2)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.address2 ? convertForShowData(item.address2) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.City)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.city ? convertForShowData(item.city) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.District)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.district ? convertForShowData(item.district) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.State)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.state ? convertForShowData(item.state) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Country)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.country ? convertForShowData(item.country) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Pin)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.pincode ? convertForShowData(item.pincode) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Adhaar)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.aadhaar_no ? convertForShowData(item.aadhaar_no) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DOB)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.dob ? convertForShowData(item.dob) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Phone)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.phone ? convertForShowData(item.phone) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Marrital_Status)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.marital_status ? convertForShowData(item.marital_status) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                {item.marital_status == 'Married' ? <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Spouce_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.spouse_name ? convertForShowData(item.spouse_name) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View> : null}
                {item.marital_status == 'Married' ? <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Spouce_DOB)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.spouse_dob ? convertForShowData(item.spouse_dob) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View> : null}
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Branch_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.branch_name ? convertForShowData(item.branch_name) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Zone_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.zone_name ? convertForShowData(item.zone_name) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Created_By)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.created_by ? convertForShowData(item.created_by) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Linked_TE_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.linked_te_name ? convertForShowData(item.linked_te_name) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderItem_two = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Address_1)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.address1 ? convertForShowData(item.address1) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Address_2)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.address2 ? convertForShowData(item.address2) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.City)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.city ? convertForShowData(item.city) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.District)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.district ? convertForShowData(item.district) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.State)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.state ? convertForShowData(item.state) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Country)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.country ? convertForShowData(item.country) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Pin)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.pincode ? convertForShowData(item.pincode) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Adhaar)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.aadhaar_no ? convertForShowData(item.aadhaar_no) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.DOB)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.dob ? convertForShowData(item.dob) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Phone)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.phone ? convertForShowData(item.phone) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Marrital_Status)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.marital_status ? convertForShowData(item.marital_status) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                {item.marital_status == 'Married' ? <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Spouce_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.spouse_name ? convertForShowData(item.spouse_name) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View> : null}
                {item.marital_status == 'Married' ? <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Spouce_DOB)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.spouse_dob ? convertForShowData(item.spouse_dob) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View> : null}
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Branch_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.branch_name ? convertForShowData(item.branch_name) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Zone_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.zone_name ? convertForShowData(item.zone_name) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Created_By)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.created_by ? convertForShowData(item.created_by) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Linked_TE_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.linked_te_name ? convertForShowData(item.linked_te_name) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Points)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.points ? convertForShowData(item.points) : convertForShowData(textValue.No_Data_Found)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderItem_four = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Date)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.date)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Dealer)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.dealer)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Dealer_Code)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.dealer_code)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_mobile)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_branch)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_branch)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Code)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_code)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_phone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Zone_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.zone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Product_name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.product_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Product_quantity)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.product_quantity)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Points)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.point)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Status)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.status)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Verified_by)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.verified_by)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderItem_five = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Date)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.date)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Dealer)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.dealer)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Dealer_Code)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.dealer_code)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_mobile)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_branch)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_branch)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Code)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_code)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_phone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Zone_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.zone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Product_name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.product_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Product_quantity)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.product_quantity)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Points)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.point)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Status)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.status)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Verified_by)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.verified_by)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderItem_six = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Phone)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.contact)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Points)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.points)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_category)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_category)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Branch_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.branch_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Zone_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.zone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Code)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_code)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Te_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.te_mobile)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderItem_seven = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Date)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.date)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Order_no)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.order_no)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_phone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Employee_name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.employee_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Employee_Id)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.employee_id)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Branch_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.branch)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Address_1)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.address1)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Address_2)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.address2)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.City)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.city)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.District)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.district)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.State)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.state)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Country)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.country)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Pin)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.pincode)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Catalogue)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.catalogue)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Status)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.status)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Delivery_Date)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.delivery_date)}</Text>
                    </View>
                </View>
            </View>
        )

    }

    const renderItem_eight = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Order_no)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.order_no)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_phone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Employee_name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.employee_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Employee_Id)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.employee_id)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Branch_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.branch)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Type)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.type)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Comment)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.comment)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Status)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.status)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Updated_at)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.updated_at)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.District)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.district)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.State)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.state)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Country)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.country)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Pin)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.pincode)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Catalogue)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.catalogue)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Status)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.status)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Delivery_Date)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.delivery_date)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderItem_nine = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Order_no)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.order_no)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_phone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Employee_name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.employee_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Employee_Id)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.employee_id)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Branch_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.branch)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Comment)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.comment)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Status)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.status)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Updated_at)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.updated_at)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.District)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.district)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.State)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.state)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Country)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.country)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Pin)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.pincode)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Catalogue)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.catalogue)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderItem_ten = ({ item, index }) => {
        return (
            <View style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Order_no)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.order_no)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Name)} :</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Mason_Phone)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.mason_phone)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Employee_name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.employee_name)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Employee_Id)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.employee_id)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Branch_Name)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.branch)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Type)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.type)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Comment)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.comment)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Status)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.status)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Updated_at)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.updated_at)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.District)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.district)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.State)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.state)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Country)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.country)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Pin)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.pincode)}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{convertForShowData(textValue.Catalogue)}:</Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{convertForShowData(item.catalogue)}</Text>
                    </View>
                </View>
            </View>
        )
    }

    const renderFooter = () => {
        if (!loading) {
            return null
        } else {
            return (
                <View style={{ paddingHorizontal: 20, paddingTop: 20, alignItems: 'center', }}>
                    <ActivityIndicator size='large' color='#ee1d23' />
                </View>
            )
        }
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
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{convertForShowData(textValue.DASHBOARD_DETAILS)}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {
                            setDetails('')
                            setCategory('')
                            props.navigation.goBack()
                        }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 10 }}>
                        {category == '1' ? <View style={{ height: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_one}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '2' ? <View style={{ height: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_two}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '3' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '4' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_four}
                                ListFooterComponent={renderFooter}
                                keyExtractor={index => index.toString()}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                key={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '5' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_five}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                                key={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '6' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_five}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                ListFooterComponent={renderFooter}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '7' ? <View style={{ width: '100%' }}>
                            <FlatList
                                data={details}
                                renderItem={renderItem_six}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                                key={index => index.toString()}
                            />
                        </View> : null}

                        {category == '8' ? <View style={{ width: '100%' }}>
                            <FlatList
                                data={details}
                                renderItem={renderItem_seven}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            />
                        </View> : null}

                        {category == '9' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_seven}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '10' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_seven}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                ListFooterComponent={renderFooter}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '11' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_eight}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '12' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_eight}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '13' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_eight}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '14' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_nine}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                        {category == '15' ? <View style={{ width: '100%' }}>
                            {details.length > 0 || loading ? <FlatList
                                data={details}
                                renderItem={renderItem_ten}
                                ListFooterComponent={renderFooter}
                                ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                                showsHorizontalScrollIndicator={false}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={index => index.toString()}
                            /> : <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: 16, fontWeight: '800' }}>{convertForShowData(textValue.No_Data_Found)}</Text>
                            </View>}
                        </View> : null}

                    </View>
                </View>
            </View>
        </SafeView>
    )
}

export default DashboardTeDetails