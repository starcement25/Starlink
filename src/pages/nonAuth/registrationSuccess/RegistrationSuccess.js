import React, { useEffect } from 'react'
import { SafeAreaView, Text, View, Image, } from 'react-native'
import styles from './RegistrationSuccessStyle'
import useTextValue from '../../../helper/constants/useTextValue'
import Icons from '../../../helper/image/ImageList'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const RegistrationSuccess = (props) => {
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
            props.navigation.navigate('Dashboard')
        }, 2500)
    }

    return (
       <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles.container}>
                <Image style={styles._img} source={Icons.tick} />
                <Text style={styles._txt}>{convertForShowData(textValue.Registration)}</Text>
                <Text style={styles._txt}>{convertForShowData(textValue.Successful)}</Text>
            </View>
        </SafeView>
    )
}

export default RegistrationSuccess