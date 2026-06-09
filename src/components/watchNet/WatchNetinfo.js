import React from 'react'
import { StyleSheet, Text, View, Modal } from 'react-native'
import { useNetInfo } from '@react-native-community/netinfo'
import useTextValue from '../../helper/constants/useTextValue'
import { convertForShowData } from '../../helper/constants/NumberConverter'
export default function WatchNetinfo() {
    const textValue = useTextValue()
    const netInfo = useNetInfo()
    return (
        <View>
            {netInfo.isConnected ? null : (<View style={styles.container}>
                <Modal animationType={'fade'} visible={true} transparent={true}>
                    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#00000050', }}>
                        <View style={styles.modal}>
                            <Text style={{ color: '#000000' }}>{convertForShowData(textValue.No_Internet_connection)}</Text>
                        </View>
                    </View>
                </Modal>
            </View>)}
        </View>
    )
}

const styles = StyleSheet.create({
    container: {
        backgroundColor: '#ecf0f1',
        position: 'absolute',
        height: '100%',
        width: '100%',
    },
    hider: {
        display: 'none',
    },
    modal: {
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#ffffff',
        height: '25%',
        width: '80%',
        borderRadius: 10,
        borderWidth: 0,
        borderColor: '#fff',
    },
    text: {
        color: '#3f2949',
        fontSize: 18,
        fontWeight: '600'
    },
})