import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container:
    {
        flex: 1,
    },
    _bgColor:
    {
        backgroundColor: '#ee1d23',
        position: 'relative',
        flex: 1
    },

    _upperView:
    {
        height: 150,
        position: 'relative',
        justifyContent: 'center',
        alignItems: 'center',

        _txt: {
            color: '#fff',
            fontSize: 20,
            fontWeight: '500'
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

    _lowerView:
    {
        backgroundColor: '#fff',
        flex: 1,
        borderTopRightRadius: 100,
        position: 'relative',
        alignItems: 'center',
        overflow: 'hidden',

        _title: {
            fontSize: 16,
            fontWeight: '700',
            color: '#EE1D23',
            marginTop: '6%',
            marginBottom: '2%'
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
                borderRadius: 10,
                overflow: 'hidden',
                position: 'relative',

                _info_icon_view: {
                    position: 'absolute',
                    top: 0,
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
        }
    },
    _horizontal_scroll: {
        marginTop: '5%',
        marginBottom: '5%',
        marginRight: '10%',
        marginLeft: '2%',

        _touchableOpacity: {
            margin: 5,
            height: 25,
            paddingLeft: 8,
            paddingRight: 8,
            justifyContent: 'center',
            alignItems: 'center',
            borderRadius: 4,

            _txt: {
                fontSize: 10
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

            _txt: {
                fontSize: 12
            },

            _btn: {
                height: 30,
                width: 70,
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
            }
        },
        _confirm_view: {
            backgroundColor: '#fff',
            borderRadius: 5,
            padding: 16,
            alignItems: 'center',
            width: '80%',

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
                    width: '40%',
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
                }
            }
        }
    }

})