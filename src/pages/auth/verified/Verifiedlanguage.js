import React, { useEffect } from 'react'
import { Text, View, Image, } from 'react-native'
import styles from './VerifiedStyle'
import useTextValue from '../../../helper/constants/useTextValue'
import Icons from '../../../helper/image/ImageList'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const Verifiedlanguage = (props) => {
    const textValue = useTextValue()

    useEffect(() => {
        fadeIn()
        const focusListener = props.navigation.addListener('focus', () => {
            fadeIn()
        })
        return focusListener
    }, [props.navigation])

    const fadeIn = () => {
        setTimeout(() => {
            props.navigation.navigate('DrawerStack')
        }, 2000)
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._view}>
                <Image source={Icons.tick} />
                <Text style={styles._view._txt}>{convertForShowData(textValue.Language_change_successfully)}</Text>
            </View>
        </SafeView>
    )
}

export default Verifiedlanguage