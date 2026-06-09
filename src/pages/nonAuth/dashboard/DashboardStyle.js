import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container:
    {
        flex: 1,
        position: 'relative',

    },
    _bgColor: {
        backgroundColor: '#ee1d23',
        flex: 1
    },
    _user_option: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        width: '100%',
        height: 40,
        alignItems: 'center',

        _view: {
            flex: 1,
            alignItems: 'center',

            _img: {
                height: 20,
                width: 20
            },
            _txt: {
                color: '#fff',
                fontSize: 11
            }
        }
    },
    _user_info: {
        flexDirection: 'row',
        width: '90%',
        height: 75,
        alignItems: 'center',
        justifyContent: 'center',

        _profile: {
            height: 75,
            width: 75,

            _img: {
                height: '100%',
                width: '100%',
                borderRadius: 50
            }
        },

        _eng_txt: {
            fontSize: 16, fontWeight: '600', color: '#000000'
        },
        _eng_txt_1: {
            fontSize: 16, fontWeight: '600', color: '#666'
        },
        _mobile_section: {
            flexDirection: 'row',
            alignItems: 'center',

            _img: {
                height: 15,
                width: 15
            },
            _txt: {
                fontSize: 16, fontWeight: '600', color: '#000'
            }
        },

        _user_type: {
            width: 125,
            backgroundColor: '#ffffff50',
            borderRadius: 20,
            justifyContent: 'center',
            alignItems: 'center',
            marginTop: 8,

            _txt: {
                fontSize: 12,
                color: '#fff'
            }
        }
    },
    _upperView: {
        height: 180,
        alignItems: 'center',
        position: 'relative',
        paddingHorizontal: 30
    },
    _lowerView: {
        backgroundColor: '#fff',
        flex: 1,
        borderTopRightRadius: 100,
        position: 'relative',

        _btn: {
            backgroundColor: '#ee1d23',
            height: 45,
            width: '75%',
            borderRadius: 25,
            marginTop: '5%',
            justifyContent: 'center',
            alignItems: 'center',

            _txt: {
                color: '#fff',
                fontWeight: '500',
                fontSize: 16,
                fontWeight: '600',
                textShadowColor: 'rgba(0, 0, 0, 0.75)',
                textShadowOffset: { width: -1, height: 1 },
                textShadowRadius: 10
            }
        }
    },
    bg:
    {
        height: 200,
        width: 200,
        resizeMode: 'cover',
    },

    _user_option_info_view: {
        alignItems: 'center',
        width: '100%',
        backgroundColor: '#FFF',
        borderRadius: 20,
        paddingVertical: 10,
    },

    _role_wise_view: {
        width: '100%',
        alignItems: 'center',
        position: 'relative'
    },

    _footer_section: {
        width: '100%',
        height: 50,
        flexDirection: 'row',
        justifyContent: 'space-evenly',
        backgroundColor: '#fff',

        _blank_touchableOpacity: {
            flex: 1,
            height: '100%',
            width: '100%',
            justifyContent: 'center',
            alignItems: 'center'
        },

        _touchableOpacity: {
            flex: 2,
            height: '100%',
            width: '100%',
            justifyContent: 'center',
            alignItems: 'center'
        }
    },

    _app_icon: {
        width: 50,
        height: 50,
        position: 'absolute',
        right: '5%',
        top: 50
    },

    _scroll_view: {
        width: '100%'
    },
    _slidder_img_box:
    {
        justifyContent: 'center',
        alignItems: 'center',
        height: 200,
        width: '100%',
        marginTop: -75,

        _view:
        {
            height: '100%',
            width: '100%',
            paddingHorizontal: 20,
            borderRadius: 10,
            overflow: 'hidden',
            backgroundColor: '#fff'
        }
    },

    _button_section_view:
    {
        width: '100%',
        justifyContent: 'center',
        alignItems: 'center',

        _view:
        {
            width: '100%',
        }
    }

})