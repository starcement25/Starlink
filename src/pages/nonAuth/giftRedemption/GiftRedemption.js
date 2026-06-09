import React from 'react'
import { SafeAreaView, Text, View, Image, TouchableOpacity, FlatList } from 'react-native'
import styles from './GiftRedemptionStyle'
import { ScrollView } from 'react-native-virtualized-view'
import useTextValue from '../../../helper/constants/useTextValue'
import Icons from '../../../helper/image/ImageList'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'

const data = [
    { id: 'a', value: 'A' },
    { id: 'b', value: 'B' },
    { id: 'c', value: 'C' },
    { id: 'd', value: 'D' }
]

const GiftRedemption = (props) => {
    const textValue = useTextValue()

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={styles._bgColor}>
                <View style={styles._upperView}>
                    <TouchableOpacity style={styles._upperView._back_btn}
                        onPress={() => {
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}
                    >
                        <Image style={styles._upperView._back_btn._img} source={Icons.back} />
                    </TouchableOpacity>
                    <Text style={styles._upperView._txt}>{convertForShowData(textValue.GIFT_REDEMPTION)}</Text>

                </View>

                <ScrollView style={styles._lowerView}>
                    <View style={{ alignItems: 'center' }}>
                        <View style={styles._lowerView._txt_input_view}>
                            <View style={styles._lowerView._txt_input_view._view}>
                                <Text style={styles._lowerView._txt_input_view._view._floating_txt}>{convertForShowData(textValue.Address)}</Text>
                                <Text style={styles._lowerView._txt_input_view._view._txt}>{convertForShowData(textValue.t0)}</Text>
                                <Image style={styles._lowerView._txt_input_view._view._icon} source={Icons.camera} />
                            </View>
                        </View>

                        <View style={styles._total_bags_view}>
                            <View style={styles._total_bags_view._view}>
                                <Text style={styles._total_bags_view._view._total_bags_txt}>{convertForShowData(textValue.Total_Bags)}</Text>
                                <Text style={styles._total_bags_view._view._total_bags_count}>{convertForShowData('567')}</Text>
                            </View>
                            <View style={styles._total_bags_view._white_radius_view}>
                                <Text style={styles._total_bags_view._white_radius_view._count}>{convertForShowData('1000')}</Text>
                                <Text style={styles._total_bags_view._white_radius_view._txt}>{convertForShowData(textValue.Total_Bags)}</Text>
                            </View>
                        </View>

                        <View style={styles._gift_view}>
                            <FlatList
                                data={data}
                                keyExtractor={item => item.id}
                                numColumns={2} 
                                renderItem={({ item }) => (
                                    <View style={styles._gift_view._renderView}>
                                        <View style={styles._gift_view._renderView._view}>
                                            <Image style={styles._gift_view._renderView._view._img} source={Icons.gift} />
                                            <View style={styles._gift_view._renderView._view._point_txt_view}>
                                                <Text style={styles._gift_view._renderView._view._point_txt_view._txt}>{convertForShowData(textValue.Points)} {convertForShowData('1000')} - {convertForShowData('5000')}</Text>
                                            </View>
                                        </View>
                                    </View>
                                )}/>
                        </View>

                    </View>
                </ScrollView>
            </View>
        </SafeView>
    )
}

export default GiftRedemption