import React, { useEffect, useRef } from 'react'
import { Text, View, Image, TouchableOpacity, PermissionsAndroid, Platform } from 'react-native'
import { launchCamera, launchImageLibrary } from 'react-native-image-picker'
import RBSheet from 'react-native-raw-bottom-sheet'
import styles from '../camera/CameraStyle'
import useMessageList from '../../helper/constants/useMessageList'
import useTextValue from '../../helper/constants/useTextValue'
import Icons from '../../helper/image/ImageList'
import { convertForShowData } from '../../helper/constants/NumberConverter'

export default function Camera(props) {
    const textValue = useTextValue()
    const messageList = useMessageList()
    const refRBSheet = useRef()

    useEffect(() => {
        refRBSheet.current.open()
    }, [])

    function selectFile(type) {
        if (type == 'CANCEL') {
            refRBSheet.current.close()
            props.sendData({ res: null })
        } else {
            const options = {
                storageOptions: { path: 'images', mediatype: 'photo', },
                includeBase64: true,
                quality: 0
            }
            var launch_mode = type === 'CAMERA' ? launchCamera : launchImageLibrary
            launch_mode(options, response => {
                refRBSheet.current.close()
                if (response.didCancel) {
                    props.sendData({ res: null })
                } else if (response.error) {
                    props.sendData({ res: null })
                } else if (response.customButton) { } else {
                    props.sendData({ res: response })
                }
            })
        }
    }

    function check_permission() {
        if (Platform.OS === 'android') {
            PermissionsAndroid.request(PermissionsAndroid.PERMISSIONS.CAMERA, {
                title: textValue.Camera,
                message: messageList.t1,
            }).then(() => {
                selectFile('CAMERA')
            })
        } else if (Platform.OS === 'ios') {
            selectFile('CAMERA')
        }
    }

    return (
        <View >
            <RBSheet
                ref={refRBSheet}
                height={180}
                closeOnPressMask={false}
                openDuration={250}
                customStyles={{
                    container: { justifyContent: 'center', alignItems: 'center', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 10, },
                }}>
                <View style={styles.view}>
                    <Text style={styles.view.header}>{convertForShowData(textValue.Select_options)}</Text>
                    <TouchableOpacity onPress={() => { check_permission() }} style={styles.view.option}>
                        <Image style={{ width: 28, height: 20, tintColor: '#868686' }} source={Icons.rs_camera} />
                        <Text style={styles.view.option.text}>{convertForShowData(textValue.Camera)}</Text>
                    </TouchableOpacity>
                    <TouchableOpacity onPress={() => selectFile('LIBRARY')} style={styles.view.option}>
                        <Image style={{ width: 25, height: 25 }} source={Icons._images} />
                        <Text style={styles.view.option.text}>{convertForShowData(textValue.Library)}</Text>
                    </TouchableOpacity>
                    <TouchableOpacity onPress={() => selectFile('CANCEL')} style={styles.view.option}>
                        <Image style={{ width: 22, height: 22 }} source={Icons.rs_cancel} />
                        <Text style={styles.view.option.text}>{convertForShowData(textValue.Close)}</Text>
                    </TouchableOpacity>
                </View>
            </RBSheet>
        </View>
    )
}
