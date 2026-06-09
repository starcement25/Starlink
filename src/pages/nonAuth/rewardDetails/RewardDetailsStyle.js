import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container:
    {
        flex: 1,
    },
    _bgColor: {
        backgroundColor: '#ee1d23',
        flex: 1
    },
    _upperView: {
        height: 150,
        alignItems: 'center',
        position: 'relative',
        justifyContent: 'center',

        _back_btn: {
            position: 'absolute',
            top: 10,
            right: 10,

            _img: {
                height: 30,
                width: 30,
            }
        },
        _txt: {
            fontSize: 20,
            color: '#fff',
            fontWeight: '600',
            // marginTop: '10%'
            // marginBottom: 20
        },
        _search_input: {
            backgroundColor: '#ffffff30',
            height: 45,
            width: '90%',
            borderRadius: 25,
            flexDirection: 'row',
            // marginTop: '5%',

            _txt_input: {
                width: '85%',
                paddingLeft: 15,
                color: '#fff'
            },

            _search_icon_view: {
                height: 40,
                width: 50,
                justifyContent: 'center',
                alignItems: 'center',

                _img: {
                    height: 20,
                    width: 20
                }
            }
        }
    },

    _lowerView: {
        backgroundColor: '#fff',
        flex: 1,
        borderTopRightRadius: 100,
        position: 'relative',
        overflow: 'hidden',

        _scroll: {
            flex: 1,
            marginTop: '0%',
            // borderTopRightRadius: 100,

            _card: {
                alignItems: 'center',
                marginTop: '10%',

                _view: {
                    height: 50,
                    width: '90%',
                    backgroundColor: '#f3f2f2',
                    borderRadius: 25,
                    shadowColor: '#000',
                    shadowOffset: { width: 1, height: 1 },
                    shadowOpacity: 0.2,
                    shadowRadius: 3,
                    position: 'relative',
                    alignItems: 'center',
                    flexDirection: 'row',

                    _txt_view: {
                        flex: 2,
                        height: 50,
                        alignItems: 'center',
                        justifyContent: 'center',
                        position: 'relative',

                        _txt: {
                            fontWeight: '500',
                            fontSize: 12
                        },
                        _line: {
                            width: 1,
                            height: '75%',
                            backgroundColor: '#ffffff',
                            position: 'absolute',
                            right: 0
                        }
                    },
                    _points: {
                        flex: 1,
                        height: 50,
                        justifyContent: 'center',
                        alignItems: 'center',

                        _counter: {
                            fontWeight: '600',
                            fontSize: 16
                        },
                        _txt: {
                            fontWeight: '400',
                            fontSize: 10
                        }
                    }
                }
            }

        }
    },

    _table_view: {
        width: '100%',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: 9,
        marginBottom: 75,

        _header_view: {
            height: 50,
            width: '90%',
            backgroundColor: '#EE1D23',
            flexDirection: 'row',
            justifyContent: 'space-evenly',

            _view: {
                flex: 1,
                justifyContent: 'center',
                alignItems: 'center',

                _txt: {
                    color: '#fff',
                    fontWeight: '800',
                    fontSize: 12
                }
            }
        },
        _content: {
            minHeight: 25,
            maxHeight: 100,
            width: '90%',
            flexDirection: 'row',
            justifyContent: 'space-evenly',

            _view_1: {
                flex: 1,
                justifyContent: 'center',
                alignItems: 'center',
                borderRightColor: '#EE1D23',
                borderBottomColor: '#00000000',
                borderLeftColor: '#EE1D23',
                borderTopColor: '#00000000',
                borderWidth: 1,
                // backgroundColor: '#fff'
            },
            _view_2: {
                flex: 1,
                justifyContent: 'center',
                alignItems: 'center',
                borderRightColor: '#EE1D23',
                borderBottomColor: '#00000000',
                borderLeftColor: '#00000000',
                borderTopColor: '#00000000',
                borderWidth: 1,
                // backgroundColor: '#fff'
            },

            _txt: {
                color: '#21211F',
                fontWeight: '600',
                fontSize: 12
            }
        },

        _bottom_line: {
            width: '90%',
            height: 1,
            backgroundColor: '#EE1D23'
        }
    },

    _dropdown_view: {
        width: 300,
        height: 40,
        flexDirection: 'row',
        justifyContent: 'space-evenly',
        marginTop: 10,
        zIndex: 999,
        // alignItems: 'center',
        position: 'relative',

        _left_view: {
            width: 130,
            height: 35,
            backgroundColor: '#fff',
            borderColor: '#D5D5D5',
            borderWidth: 1,
            borderRadius: 5,
            zIndex: 999,
        },
        _right_view: {
            width: 100,
            height: 35,
            backgroundColor: '#fff',
            borderColor: '#D5D5D5',
            borderWidth: 1,
            borderRadius: 5,
            zIndex: 999
        }
    },

    _center_dropdown_view: {
        width: '100%',
        alignItems: 'center',
        zIndex: 999,
        marginTop: 10
    },

    _btn_view: {
        width: '100%',
        alignItems: 'center',
        marginTop: -70,

        _btn: {
            backgroundColor: '#ee1d23',
            height: 45,
            width: '75%',
            borderRadius: 25,
            marginTop: '3%',
            marginBottom: '3%',
            justifyContent: 'center',
            alignItems: 'center',

            _txt: {
                color: '#fff',
                fontWeight: '500',
                fontSize: 14
            }
        }
    },

    _ndf: {
        width: '90%',
        alignItems: 'center',
        marginTop: '5%',

        _img: {
            height: 100,
            resizeMode: 'contain'
        }
    }
})
