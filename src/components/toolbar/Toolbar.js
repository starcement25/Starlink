import React from 'react'
import { Text, View, StyleSheet, TouchableOpacity, Image } from 'react-native'
import Icon from 'react-native-vector-icons/FontAwesome'
import Dimension from '../../helper/dimension/Dimension'
import { useNavigation } from '@react-navigation/native'
import Icons from '../../helper/image/ImageList'
import { convertForShowData } from '../../helper/constants/NumberConverter'
import ImagePath from '../../image/ImagePath'

export default function Toolbar(props) {
    const navigation = useNavigation()

    return (
        <View style={[styles.container, { position: props?.obj?.icon == 'notification' ? 'relative' : 'absolute', top: 0 }]}>
            <Text style={styles.titleStyle}>
                {convertForShowData(props.obj.title)}
            </Text>
            <TouchableOpacity style={styles.container.navIcon} onPress={() => { navigation.toggleDrawer() }} activeOpacity={0.6}>
                <Icon name={'bars'} size={25} color='#fff' />
            </TouchableOpacity>
            {props.obj.icon == 'notification' ? <TouchableOpacity style={styles.container.bellIcon} onPress={() => { navigation.navigate('Notification') }} activeOpacity={0.6}>
                <Image source={ImagePath.NotificationIcon} style={{ width: 35, height: 35 }} />
            </TouchableOpacity> : <TouchableOpacity style={styles.container._back} onPress={() => navigation.goBack()}>
                <Image style={styles.container._back._img} source={Icons.back} />
            </TouchableOpacity>}
        </View>
    )
}

const styles = StyleSheet.create({
    container: {
        backgroundColor: 'transparent',
        height: 70,
        width: '100%',
        justifyContent: 'center',
        shadowColor: '#000',
        shadowOpacity: 0.2,
        shadowOffset: { width: 1, height: 1 },
        elevation: Dimension(4),

        navIcon: {
            position: 'absolute',
            left: 0,
            height: 50,
            width: 50,
            justifyContent: 'center',
            alignItems: 'center',
        },
        bellIcon: {
            position: 'absolute',
            right: 20,
            height: 50,
            width: 50,
            justifyContent: 'center',
            alignItems: 'center',
        },
        _back: {
            position: 'absolute',
            top: 10,
            right: 10,

            _img: {
                height: 30,
                width: 30,
            }
        }
    },
    titleStyle: {
        fontSize: Dimension(18),
        fontWeight: '600',
        color: '#fff',
        position: 'absolute',
        left: 60
    }
})