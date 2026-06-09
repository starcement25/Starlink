import React from 'react'
import { SafeAreaView, StatusBar, KeyboardAvoidingView, Platform, View } from 'react-native'
import PropTypes from 'prop-types'
import { useSafeAreaInsets } from 'react-native-safe-area-context'
import { Colors } from './Colors'
export default function SafeView(props) {
    const insets = useSafeAreaInsets()
    const statusBarHeight = Platform.OS === 'android' ? StatusBar.currentHeight : insets.top;

    return (
        <>
            <KeyboardAvoidingView
                behavior={Platform.OS === 'ios' ? 'padding' : null}
                style={{ height: '100%' }}>
                <View
                    style={{
                        height: statusBarHeight,
                        backgroundColor: Colors.red,
                    }}
                />
                <StatusBar barStyle={'light-content'} hidden={false} backgroundColor={Colors.red} translucent={true} />
                <SafeAreaView style={{ backgroundColor: props.backgroundColor, flex: 1, paddingBottom: Platform.OS === 'android' ? insets.bottom : 0 }}>
                    <View style={{ width: '100%', height: '100%' }}>
                        {props.children}
                    </View>
                </SafeAreaView>
            </KeyboardAvoidingView>
        </>
    )
}
SafeView.propTypes = {
    backgroundColor: PropTypes.string,
    bar: true
}

SafeView.defaultProps = {
    backgroundColor: 'rgb(255, 255, 255)'
}