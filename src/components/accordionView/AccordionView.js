import React, { useState } from 'react'
import { View, Text, StyleSheet, TouchableOpacity, Image } from 'react-native'
import Icons from '../../helper/image/ImageList'
import { convertForShowData } from '../../helper/constants/NumberConverter'

export default function AccordionView(props) {
    const [isCollapsed, setIsCollapsed] = useState(true)
    return (
        <View style={{ alignItems: 'center', width: '100%' }}>
            <TouchableOpacity activeOpacity={0.8} onPress={() => { setIsCollapsed(!isCollapsed) }} style={isCollapsed ? styles.before : styles.after}>
                <Text style={styles._title}>{convertForShowData(props.obj.title)}</Text>
                <View style={styles._img_view}>
                    <Image source={Icons.arrow_icon} style={[styles._arrow_icon, { transform: [{ rotate: isCollapsed ? '180deg' : '0deg' }] }]} />
                </View>
            </TouchableOpacity>
            {!isCollapsed?<View style={{ backgroundColor: '#E9E8E861', width: '90%', paddingHorizontal: 10,paddingVertical:10 }}>
                <Text style={{ fontSize: 12, color: '#000', width: '100%' }}>{convertForShowData(props.obj.content)}</Text>
            </View>:null}
        </View>
    )
}
const styles = StyleSheet.create({
    before: { backgroundColor: '#E9E8E8D9', height: 50, width: '90%', justifyContent: 'center', borderRadius: 5, position: 'relative', marginTop: 10, paddingLeft: 16 },
    after: { backgroundColor: '#E9E8E8D9', height: 50, width: '90%', justifyContent: 'center', borderTopLeftRadius: 5, borderTopRightRadius: 5, position: 'relative', marginTop: 10, paddingLeft: 16 },
    _title: { color: '#000000', fontWeight: '600' },
    _arrow_icon: { tintColor: '#000000', height: 8, width: 15 },
    _collapsible_view: {

        _view: {
            width: '95%', padding: 10,
        }
    },
    _img_view: { height: '100%', width: 30, position: 'absolute', backgroundColor: '#E9E8E8D9', right: 0, justifyContent: 'center', alignItems: 'center', borderRadius: 5, }
})
