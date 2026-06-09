import React from 'react'
import { Text, View, Image, TouchableOpacity } from 'react-native'
import styles from './WelcomeStyle'
import useTextValue from '../../../helper/constants/useTextValue'
import Icons from '../../../helper/image/ImageList'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const Welcome = (props) => {
    const textValue = useTextValue()
    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._bgColor}>
                <View style={styles._upperView}>
                    <View style={styles._circle}>
                        <Image style={styles._circle._img} source={Icons.star_cement_logo} />
                    </View>
                </View>
                <View style={styles._lowerView}>
                    <View style={styles.bg}>
                        <Image style={styles.bg._img} source={Icons.actor_bg} />
                    </View>
                    <Image style={styles._lowerView.authIcon} source={Icons.auth_user_icon}></Image>
                    <Text style={styles._lowerView._loginTxt}>{convertForShowData(textValue.Login)}</Text>
                    <TouchableOpacity
                        onPress={() => props.navigation.navigate('Login')}
                        style={styles._lowerView._loginBtn}>
                        <Text style={styles._lowerView._loginBtn._txt}>{convertForShowData(textValue.Login)}</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </SafeView>
    )
}

export default Welcome
