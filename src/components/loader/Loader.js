import React from 'react'
import { View, ActivityIndicator } from 'react-native'
import styles from './LoaderStyle'

export default function Loader() {
    return (
        <View style={styles.container}>
            <ActivityIndicator size='large' color='#ee1d23' />
        </View>
    )
}