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
            marginBottom: 20
        },
        _search_input: {
            backgroundColor: '#ffffff30',
            height: 45,
            width: '90%',
            borderRadius: 25,
            flexDirection: 'row',

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

            _view: {
                width: '100%',
                alignItems: 'center',

                _user_img: {
                    height: 70,
                    width: 70,
                    borderRadius: 50,
                    marginBottom: -40,
                    zIndex: 9
                }
            },
            _card_view: {
                height: 200,
                width: '90%',
                backgroundColor: '#f3f2f2',
                borderRadius: 40,
                shadowColor: '#000',
                shadowOffset: { width: 0, height: 1 },
                shadowOpacity: 0.2,
                shadowRadius: 3,
                position: 'relative',
                alignItems: 'center',
                paddingTop: 50,

                _txt: {
                    fontSize: 14,
                    fontWeight: '500'
                },

                _date: {
                    flexDirection: 'row',

                    _icon: {
                        height: 18,
                        width: 18
                    }
                },

                _line: {
                    height: 1,
                    width: '90%',
                    backgroundColor: 'gray',
                    marginTop: 5,
                    marginBottom: 5
                },

                _txt_area: {
                    padding: 10,
                    _txt: {
                        fontSize: 12
                    }
                }
            }
        }
    },

    _history_view: {
        width: '100%',
        alignItems: 'center',
        overflow: 'hidden',

        _card: {
            width: '90%',
            backgroundColor: '#F3F2F2',
            borderRadius: 10,
            shadowColor: '#000',
            shadowOffset: { width: 0, height: 1 },
            shadowOpacity: 0.2,
            shadowRadius: 3,
            position: 'relative',
            padding: 10,

            _txt_view: {
                flexDirection: 'row',
                justifyContent: 'space-between',

                _right_txt: {
                    fontSize: 12,
                    color: '#21211F'
                },
                _left_txt: {

                    fontSize: 12,
                    color: '#EE1D23'
                }
            },
            _line: {
                height: 1,
                width: '100%',
                backgroundColor: '#BCBCBC',
                marginBottom: 5,
                marginTop: 5
            }
        }
    },

    _dropdown_view: {
        width: 300,
        height: 40,
        flexDirection: 'row',
        justifyContent: 'space-evenly',
        marginTop: 10,
        marginBottom: 25,
        zIndex: 999,
        position: 'relative',

        _left_view: {
            width: 130,
            height: 35,
            backgroundColor: '#fff',
            borderColor: '#D5D5D5',
            borderWidth: 1,
            borderRadius: 5,
        },
        _right_view: {
            width: 100,
            height: 35,
            backgroundColor: '#fff',
            borderColor: '#D5D5D5',
            borderWidth: 1,
            borderRadius: 5
        }
    },

    _table_view: {
        width: '100%',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: 99,
        marginBottom: 20,

        _title_view: {
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

        _content_view: {
            minHeight: 25,
            maxHeight: 100,
            width: '90%',
            backgroundColor: '#fff',
            flexDirection: 'row',
            justifyContent: 'space-evenly',

            _view: {
                flex: 1,
                justifyContent: 'center',
                alignItems: 'center',
                borderRightColor: '#EE1D23',
                borderBottomColor: '#00000000',
                borderLeftColor: '#EE1D23',
                borderTopColor: '#00000000',
                borderWidth: 1,

                _txt: {
                    color: '#21211F',
                    fontWeight: '600',
                    fontSize: 12
                }
            },

            _view_middle: {
                flex: 1,
                justifyContent: 'center',
                alignItems: 'center',
                borderRightColor: '#EE1D23',
                borderBottomColor: '#00000000',
                borderLeftColor: '#00000000',
                borderTopColor: '#00000000',
                borderWidth: 1,

                _edit_icon: {
                    width: '100%',
                    justifyContent: 'center',
                    alignItems: 'center'
                }
            }
        },

        _bottom_line: {
            width: '90%',
            height: 1,
            backgroundColor: '#EE1D23'
        }
    },

    _modal_view: {
        height: '100%',
        width: '100%',
        backgroundColor: '#00000095',
        justifyContent: 'center',
        alignItems: 'center',

        _view: {
            overflow: 'hidden',
            width: '90%',
            backgroundColor: '#fff',
            borderRadius: 10,
            padding: 10,
            position: 'relative',

            _header: {
                textAlign: 'center',
                fontSize: 18,
                fontWeight: '600',
                marginBottom: 15,
                marginTop: 10
            }
        },

        _input_section: {
            flex: 1,
            alignItems: 'center',

            _dropdown_start_view: {
                width: '90%',
                height: 15,
                marginBottom: -35,
                position: 'relative',

                _txt: {
                    color: '#ee1d23',
                    position: 'absolute',
                    right: 0,
                    top: -2
                },

                _dropdown_view: {
                    zIndex: 999,
                    marginTop: 20,
                    borderRightColor: '#a8a8a8',
                    borderLeftColor: '#a8a8a8',
                    borderTopColor: '#a8a8a8',
                    borderWidth: 1,
                    borderTopEndRadius: 25,
                    borderTopStartRadius: 25,
                },
                _input_view: {
                    height: 55,
                    width: '90%',
                    borderRadius: 25,
                    backgroundColor: '#fff',
                    borderColor: '#a8a8a8',
                    borderWidth: 1,
                    justifyContent: 'center',
                    paddingLeft: 10,
                    paddingRight: 10,
                    position: 'relative',
                    marginTop: 25,

                    _start_mark: {
                        position: 'absolute',
                        top: -4,
                        right: -2,
                        color: '#ee1d23'
                    }
                }
            }
        },

        _btn_section: {
            zIndex: 99,
            width: '100%',
            height: 50,
            flexDirection: 'row',
            marginBottom: 10,

            _touchableOpacity: {
                flex: 1,
                backgroundColor: '#EE1D23',
                justifyContent: 'center',
                alignItems: 'center',
                margin: 5,
                borderRadius: 10,

                _txt: {
                    color: '#fff',
                    fontSize: 14,
                    fontWeight: '600'
                }
            }
        },

        _error_txt: {
            width: '75%',
            justifyContent: 'flex-start',

            _txt: {
                fontSize: 10,
                marginTop: 5,
                color: '#EE1D23'
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