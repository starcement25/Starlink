import React, { useEffect, useState } from 'react'
import { Image, Platform, Text, TouchableOpacity, View } from 'react-native'
import styles from './FaqStyle'
import AccordionView from '../../../components/accordionView/AccordionView'
import { getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Loader from '../../../components/loader/Loader'
import Toast from 'react-native-toast-message'
import { ScrollView } from 'react-native-virtualized-view'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import Icons from '../../../helper/image/ImageList'

const Faq = (props) => {
    const messageList = useMessageList()

    const [loading, setLoading] = useState(false)
    const [faq, setFaq] = useState([])

    useEffect(() => {
        const focusListener = props.navigation.addListener('focus', () => {
            setLoading(true)
            getData()
        })
        return focusListener
    }, [props.navigation])

    const getData = async () => {
        getApiWithHeader(constants.get_faq + '?preferred_app_lang=' + selectedLanguage())
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    //console.log(response.data.data)
                    setFaq(response.data.data)
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

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={{ width: '100%', height: '100%', flexDirection: 'column', backgroundColor: '#FFF' }}>
                <View style={{ width: '100%', height: 100, borderBottomLeftRadius: 25, borderBottomRightRadius: 25, backgroundColor: '#EE1D23' }} />
            </View>
            <View style={{ width: '100%', height: '100%', position: 'absolute', flexDirection: 'column' }}>
                <View style={{ height: Platform.OS == 'ios' ? 25 : 0 }} />
                <View style={{ width: '100%', height: 70 }}>
                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600', marginBottom: 20 }}>{"FAQ's"}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() =>setTimeout(()=>{
                                props.navigation.goBack()
                            },500)}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: '100%', flex: 1, paddingHorizontal: 20 }}>
                    <View style={{ width: '100%', height: '100%', backgroundColor: '#FFF', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingVertical: 10, paddingHorizontal: 0 }}>
                        <ScrollView style={styles._scroll_view}>
                            {faq?.map((data, index) =>
                                <View key={index}>
                                    <AccordionView obj={{ title: data.question, content: data.answer, position: index }} ></AccordionView>
                                </View>)}
                        </ScrollView>
                    </View>
                </View>
            </View>
            {loading ? <Loader /> : null}
        </SafeView>
    )
}

export default Faq