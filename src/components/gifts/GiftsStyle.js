import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container: {
        alignItems: 'center'
    },
    before: {
        backgroundColor: '#E9E8E8D9',
        height: 50,
        width: '90%',
        justifyContent: 'center',
        borderRadius: 5,
        position: 'relative',
        marginTop: 10,
        paddingLeft: 16
    },
    after: {
        backgroundColor: '#E9E8E8D9',
        height: 50,
        width: '90%',
        justifyContent: 'center',
        borderTopLeftRadius: 5,
        borderTopRightRadius: 5,
        position: 'relative',
        marginTop: 10,
        paddingLeft: 16
    },
    _title: {
        color: '#000000',
        fontWeight: '600'
    },
    _arrow_icon: {
        tintColor: '#000000',
        height: 8,
        width: 15
    },
    _collapsible_view: {
        backgroundColor: '#fff',
        width: '95%',

        _view: {
            width: '90%',
            padding: 10,

            _txt: {
                fontSize: 12,
                color: '#000'
            }
        }
    },
    _img_view: {
        height: '100%',
        width: 30,
        position: 'absolute',
        backgroundColor: '#E9E8E8D9',
        right: 0,
        justifyContent: 'center',
        alignItems: 'center',
        borderRadius: 5,
    },
    _grid_view: {
        width: '50%',
        height: 210,
        justifyContent: 'center',
        alignItems: 'center',
        overflow: 'hidden',

        _view: {
            width: '90%',
            height: 190,
            backgroundColor: '#FFFFFF',
            borderColor: '#C8C8C8',
            borderWidth: 1,
            borderRadius: 15,
            overflow: 'hidden',
            position: 'relative',

            _info_icon_view: {
                position: 'absolute',
                top: 4,
                right: 0,
                height: 30,
                width: 30,
                zIndex: 9,
                alignItems: 'center'
            },

            _img_view:
            {
                width: '100%',
                height: 140,
                justifyContent: 'center',
                alignItems: 'center',
                padding: '5%',
                _img:
                {
                    height: '100%',
                    width: '100%',
                    resizeMode: 'contain'
                }
            },

            _txt_view:
            {
                height: 40,
                alignItems: 'center',
                padding: 4,

                _place_txt:
                {
                    fontSize: 10,
                    fontWeight: '600',
                    color: '#EE1D23'
                }
            },
            _point_view: {
                position: 'absolute',
                bottom: 4,
                width: '100%',
                alignItems: 'center',
                _point_txt: {
                    fontSize: 12,
                    color: '#EE1D23',
                    fontWeight: '800'
                }
            }
        },
        _high_light_view: {
            width: '90%',
            height: 190,
            backgroundColor: '#00000050',
            position: 'absolute',
            borderRadius: 10,
            justifyContent: 'center',
            alignItems: 'center',

            _img: {
                height: '35%',
                width: '35%',
                tintColor: '#696A6B'
            }
        }
    },
    _modal: {
        height: '100%',
        width: '100%',
        backgroundColor: '#00000095',
        justifyContent: 'center',
        alignItems: 'center',

        _info_view: {
            backgroundColor: '#fff',
            borderRadius: 5,
            padding: 16,
            alignItems: 'center',
            width: '80%',
            position: 'relative',

            _txt: {
                fontSize: 12
            },

            _btn: {
                height: 35,
                width: 35,
                justifyContent: 'center',
                alignItems: 'center',
                position: 'absolute',
                right: 0,
                top: 0,
                backgroundColor: '#fff',
                borderRadius: 5,

                _txt: {
                    color: '#fff',
                    fontWeight: '600',
                    fontSize: 14
                }
            }
        },
        _confirm_view: {
            backgroundColor: '#fff',
            borderRadius: 5,
            padding: 16,
            alignItems: 'center',
            width: '80%',
            position: 'relative',

            _txt: {
                fontSize: 14,
                fontWeight: '700'
            },
            _btn_section: {
                width: '100%',
                flexDirection: 'row',
                justifyContent: 'space-evenly',

                _btn: {
                    height: 30,
                    width: '90%',
                    backgroundColor: '#ee1d23',
                    justifyContent: 'center',
                    alignItems: 'center',
                    marginTop: 16,
                    borderRadius: 5,

                    _txt: {
                        color: '#fff',
                        fontWeight: '600',
                        fontSize: 14
                    }
                },
                _close_btn: {
                    height: 35,
                    width: 35,
                    justifyContent: 'center',
                    alignItems: 'center',
                    position: 'absolute',
                    right: 0,
                    top: 0,
                    backgroundColor: '#fff',
                    borderRadius: 5,
                }
            }
        }
    }
})