import React, { useState } from 'react'
import { Text, TouchableOpacity, View } from 'react-native'
import DataStore from '../../helper/constants/DataStore'
import useTextValue from '../../helper/constants/useTextValue'

const LanguagePicker = (props) => {
    const textValue = useTextValue()
    const [language, setLanguage] = useState(DataStore.language)
    return (
        <View style={{ width: '100%', height: '100%', position: 'absolute', backgroundColor: '#0004', alignItems: 'center', justifyContent: 'center' }}>
            <View style={{ width: '80%', padding: 20, backgroundColor: '#FFF', borderRadius: 5 }}>
                <Text style={{ fontSize: 18, fontWeight: '500', color: '#ee1d23' }}>{textValue.Select_Language}</Text>
                <View style={{ height: 20 }} />
                <TouchableOpacity onPress={() => setLanguage('English')}>
                    <View style={{ width: '100%', flexDirection: 'row', paddingHorizontal: 20, paddingVertical: 15, backgroundColor: language == 'English' ? '#ee1d2310' : '#0000' }}>
                        <Text style={{ color: '#000', flex: 1 }}>English</Text>
                        <View style={{ width: 20, height: 20, borderRadius: 10, borderWidth: 1, borderColor: language == 'English' ? '#ee1d23' : '#ddd', alignItems: 'center', justifyContent: 'center' }}>
                            <View style={{ width: 15, height: 15, borderRadius: 10, backgroundColor: language == 'English' ? '#ee1d23' : '#0000' }} />
                        </View>
                    </View>
                </TouchableOpacity>
                <View style={{ height: 10 }} />
                <TouchableOpacity onPress={() => setLanguage('Hindi')}>
                    <View style={{ width: '100%', flexDirection: 'row', paddingHorizontal: 20, paddingVertical: 15, backgroundColor: language == 'Hindi' ? '#ee1d2310' : '#0000' }}>
                        <Text style={{ color: '#000', flex: 1 }}>हिंदी</Text>
                        <View style={{ width: 20, height: 20, borderRadius: 10, borderWidth: 1, borderColor: language == 'Hindi' ? '#ee1d23' : '#ddd', alignItems: 'center', justifyContent: 'center' }}>
                            <View style={{ width: 15, height: 15, borderRadius: 10, backgroundColor: language == 'Hindi' ? '#ee1d23' : '#0000' }} />
                        </View>
                    </View>
                </TouchableOpacity>
                <View style={{ height: 10 }} />
                <TouchableOpacity onPress={() => setLanguage('Assamese')}>
                    <View style={{ width: '100%', flexDirection: 'row', paddingHorizontal: 20, paddingVertical: 15, backgroundColor: language == 'Assamese' ? '#ee1d2310' : '#0000' }}>
                        <Text style={{ color: '#000', flex: 1 }}>অসমীয়া</Text>
                        <View style={{ width: 20, height: 20, borderRadius: 10, borderWidth: 1, borderColor: language == 'Assamese' ? '#ee1d23' : '#ddd', alignItems: 'center', justifyContent: 'center' }}>
                            <View style={{ width: 15, height: 15, borderRadius: 10, backgroundColor: language == 'Assamese' ? '#ee1d23' : '#0000' }} />
                        </View>
                    </View>
                </TouchableOpacity>
                <View style={{ height: 10 }} />
                <TouchableOpacity onPress={() => setLanguage('Bengali')}>
                    <View style={{ width: '100%', flexDirection: 'row', paddingHorizontal: 20, paddingVertical: 15, backgroundColor: language == 'Bengali' ? '#ee1d2310' : '#0000' }}>
                        <Text style={{ color: '#000', flex: 1 }}>বাংলা</Text>
                        <View style={{ width: 20, height: 20, borderRadius: 10, borderWidth: 1, borderColor: language == 'Bengali' ? '#ee1d23' : '#ddd', alignItems: 'center', justifyContent: 'center' }}>
                            <View style={{ width: 15, height: 15, borderRadius: 10, backgroundColor: language == 'Bengali' ? '#ee1d23' : '#0000' }} />
                        </View>
                    </View>
                </TouchableOpacity>
                <View style={{ height: 20 }} />
                <View style={{ width: '100%', flexDirection: 'row-reverse' }}>
                    <TouchableOpacity onPress={() => props.saveLanguage(language)} style={{ paddingHorizontal: 20, paddingVertical: 8, borderRadius: 30, backgroundColor: '#ee1d23' }}>
                        <Text style={{ color: '#FFF' }}>{textValue.Save}</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    )
}
export default LanguagePicker