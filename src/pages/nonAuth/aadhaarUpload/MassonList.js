import React, { useCallback, useEffect, useRef, useState } from 'react'
import { ActivityIndicator, FlatList, Image, Platform, Text, TextInput, TouchableOpacity, View } from 'react-native'
import Icons from '../../../helper/image/ImageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { getApiWithHeader } from '../../../helper/http/Api'
import Loader from '../../../components/loader/Loader'
import DataStore from '../../../helper/constants/DataStore'
import useTextValue from '../../../helper/constants/useTextValue'
import { convertForUploadData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'

const MassonList = (props) => {
    const textValue = useTextValue()

    const [massonList, setMassonList] = useState([])
    const [actionNeedMassonList, setActionNeedMassonList] = useState([])
    const [updatedMassonList, setUpdatedMassonList] = useState([])
    const [flatlistLoader, setFlatListLoader] = useState(true)
    const [loading, setLoading] = useState(false)
    const [selectListType, setSelectListType] = useState('action_need')
    const [searchText, setSearchText] = useState('')

    const isFocusRef = useRef(false)

    useFocusEffect(
        useCallback(() => {
            //console.log('✅ Screen is focused')
            isFocusRef.current = true
            setLoading(true)
            requestForMassonList(1, [], [])
            return () => {
                //console.log('⛔ Screen is not focused')
                isFocusRef.current = false
            }
        }, [])
    )

    useEffect(() => {
        if (selectListType == 'action_need') {
            const filteredData = actionNeedMassonList.filter(item =>
                item.phone.toLowerCase().includes(convertForUploadData(searchText).toLowerCase())
            )
            setMassonList(filteredData)
        } else {
            const filteredData = updatedMassonList.filter(item =>
                item.phone.toLowerCase().includes(convertForUploadData(searchText).toLowerCase())
            )
            setMassonList(filteredData)
        }
    }, [searchText])

    useEffect(() => {
        if (!flatlistLoader) {
            if (selectListType == 'action_need') {
                setMassonList(actionNeedMassonList)
                setLoading(false)
            } else {
                setMassonList(updatedMassonList)
                setLoading(false)
            }
        } else {
            if (selectListType != 'action_need') {
                Toast.show({
                    type: 'info',
                    text1: 'Wait...',
                    text2: 'Please wait for a while...'
                })
            }
        }
        setLoading(false)
    }, [selectListType])

    const requestForMassonList = (page_value, actionNeedList, updatedList) => {
        if (!isFocusRef.current) {
            //console.log('⛔ Skipping API because screen not focused')
            return
        }
        let url = `te/get-te-masons?page=${page_value}&preferred_app_lang=` + selectedLanguage()
        //console.log(url)

        getApiWithHeader(url)
            .then(response => {
                if (!isFocusRef.current) {
                    //console.log('⛔ Skipping API because screen not focused')
                    return
                }
                if (response.data.status) {
                    setLoading(false)
                    var b = actionNeedList
                    var c = updatedList
                    for (var i = 0; i < response.data.data.length; i++) {
                        var obj = response.data.data[i]
                        var check = 1
                        if (!obj.aadhaar_doc || !obj.aadhaar_no || !obj.address1) {
                            check = 0
                        }
                        obj = { ...obj, check }
                        if (check == 0) {
                            b = [...b, obj]
                        } else {
                            c = [...c, obj]
                        }
                    }
                    setActionNeedMassonList(b)
                    setUpdatedMassonList(c)
                    setMassonList(b)
                    if (response.data.data.length != 0) {
                        requestForMassonList(page_value + 1, b, c)
                    } else {
                        setFlatListLoader(false)
                    }
                }
            })
            .catch(err => {
                //console.log(err)
                setLoading(false)
            })
    }

    const renderItem = ({ item }) => {
        return (
            <TouchableOpacity onPress={() => {
                DataStore.massonObj = item
                props.navigation.navigate('UpdateMassonProfile')
            }} style={{ width: '100%', borderWidth: 1, borderColor: '#FFDFE1', paddingHorizontal: 5, paddingVertical: 5, borderRadius: 5 }}>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Name} : </Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.name}</Text>
                    </View>
                </View>
                <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                        <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Phone} : </Text>
                    </View>
                    <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                    <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                        <Text style={{ color: 'black', fontSize: 15 }}>{item.phone}</Text>
                    </View>
                </View>
                {item.check == 0 ? null : <View style={{ width: '100%', flexDirection: 'column' }}>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Email} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.email}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Address_1} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.address1}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.City} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.city}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.District} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.district}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.State} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.state}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Pin} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.pincode}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.DOB} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.dob}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Aadhaar_no} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.aadhaar_no}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                        <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                            <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Marital_Status} : </Text>
                        </View>
                        <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                        <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                            <Text style={{ color: 'black', fontSize: 15 }}>{item.marital_status}</Text>
                        </View>
                    </View>
                    {item.marital_status_value == 1 ? <>
                        <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, backgroundColor: '#FFF0F1', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }}>
                            <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                                <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Spouse_name} : </Text>
                            </View>
                            <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                            <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                                <Text style={{ color: 'black', fontSize: 15 }}>{item.spouse_name}</Text>
                            </View>
                        </View>
                        <View style={{ flexDirection: 'row', flex: 2, minHeight: 40, padding: 5, justifyContent: 'center', alignItems: 'center' }}>
                            <View style={{ flex: 1, height: '100%', flexDirection: 'column', justifyContent: 'center' }}>
                                <Text style={{ color: 'gray', fontSize: 15 }}>{textValue.Spouse_DoB} : </Text>
                            </View>
                            <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
                            <View style={{ flex: 1.5, height: '100%', flexDirection: 'column', justifyContent: 'center', paddingLeft: 10 }}>
                                <Text style={{ color: 'black', fontSize: 15 }}>{item.spouse_dob}</Text>
                            </View>
                        </View>
                    </> : null}
                </View>}
            </TouchableOpacity>
        )
    }

    const renderFooter = () => {
        if (!flatlistLoader) return null
        return (
            <View style={{paddingHorizontal: 20,paddingTop: 20,alignItems: 'center'}}>
                <ActivityIndicator size='large' color='#ee1d23' />
            </View>
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
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{textValue.Contractor_List}</Text>
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
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 15, paddingHorizontal: 5 }}>
                        <View style={{ width: '100%', flexDirection: 'row', height: 35, backgroundColor: '#FFD5D640', borderRadius: 5 }}>
                            <TouchableOpacity onPress={() => {
                                if (selectListType != 'action_need') {
                                    setMassonList([])
                                    setSelectListType('action_need')
                                }
                            }} style={{ flex: 1, height: '100%', alignItems: 'center', justifyContent: 'center', backgroundColor: selectListType == 'action_need' ? '#ee1d23' : '#0000', borderRadius: 5 }}>
                                <Text style={{ color: selectListType == 'action_need' ? '#FFF' : 'black' }}>{textValue.Action_Need}</Text>
                            </TouchableOpacity>
                            <TouchableOpacity onPress={() => {
                                if (selectListType != 'updated') {
                                    setMassonList([])
                                    setSelectListType('updated')
                                }
                            }} style={{ flex: 1, height: '100%', alignItems: 'center', justifyContent: 'center', backgroundColor: selectListType == 'updated' ? '#ee1d23' : '#0000', borderRadius: 5 }}>
                                <Text style={{ color: selectListType == 'updated' ? '#FFF' : 'black' }}>{textValue.Updated}</Text>
                            </TouchableOpacity>
                        </View>
                        <View style={{ height: 10 }} />
                        <View style={{ width: '100%', height: 50, paddingHorizontal: 10, backgroundColor: '#FFF5F6', borderRadius: 8, borderWidth: 1, borderColor: '#FFDBDD', alignItems: 'center', justifyContent: 'center' }}>
                            <TextInput
                                style={{ width: '100%' }}
                                placeholderTextColor='#a8a8a8'
                                keyboardType='number-pad'
                                placeholder={textValue.Search_by_mobile_number}
                                onChangeText={text => { setSearchText(text) }}
                                color='#000000'/>
                        </View>
                        <View style={{ height: 10 }} />
                        <FlatList
                            showsHorizontalScrollIndicator={false}
                            showsVerticalScrollIndicator={false}
                            ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
                            data={massonList}
                            renderItem={renderItem}
                            ListFooterComponent={renderFooter}
                            keyExtractor={item => item.id}
                        />
                    </View>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default MassonList
