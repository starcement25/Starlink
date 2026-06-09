import React from 'react'

import Toast, { BaseToast, ErrorToast } from 'react-native-toast-message'
function Toast() {
    const toastConfig = {
        success: (props) => (
            <BaseToast
                {...props}
                style={styles.style}
                contentContainerStyle={styles.contentContainerStyle}
                text1Style={[styles.text1Style,{color:'#000'}]}
                text1NumberOfLines={2}
                text2Style={[styles.text2Style,{color:'#000'}]}
                text2NumberOfLines={2}
            />
        ),
        error: (props) => (
            <ErrorToast
                {...props}
                style={[styles.style, styles.errorStyle]}
                contentContainerStyle={styles.contentContainerStyle}
                text1Style={[styles.text1Style,{color:'#000'}]}
                text1NumberOfLines={2}
                text2Style={[styles.text2Style,{color:'#000'}]}
                text2NumberOfLines={2}
            />
        ),
    }
  return (
    <Toast config={toastConfig} ref={(ref) => Toast.setRef(ref)} />
  )
}

export default Toast
