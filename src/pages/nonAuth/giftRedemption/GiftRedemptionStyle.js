import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container:
    {
        flex: 1
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
        }
    },

    _total_bags_view: {
        backgroundColor: '#2ba43b',
        width: '90%',
        height: 50,
        borderRadius: 25,
        marginTop: '5%',
        justifyContent: 'center',
        position: 'relative',

        _view: {
            flexDirection: 'row',
            paddingLeft: '5%',
            alignItems: 'center',

            _total_bags_txt: {
                color: '#fff',
                fontSize: 10
            },
            _total_bags_count: {
                color: '#fff',
                fontSize: 16,
                fontWeight: '600',
                marginLeft: 5
            }
        },
        _white_radius_view: {
            width: 100,
            height: 42,
            backgroundColor: '#fff',
            position: 'absolute',
            top: 4,
            right: 4,
            borderRadius: 20,
            justifyContent: 'center',
            alignItems: 'center',

            _count: {
                color: '#000',
                fontSize: 16,
                fontWeight: '800',
                textAlign: 'center'
            },
            _txt: {
                color: '#000',
                fontSize: 10,
                textAlign: 'center'
            }
        }
    },

    _lowerView: {
        backgroundColor: '#fff',
        flex: 1,
        borderTopRightRadius: 100,
        position: 'relative',

        _scroll: {
            flex: 1,
            marginTop: '5%',
            borderTopRightRadius: 100
        },

        _txt_input_view: {
            marginTop: '5%',
            width: '90%',

            _view: {
                width: '90%',
                borderBottomColor: 'gray',
                borderTopColor: 'transparent',
                borderLeftColor: 'transparent',
                borderRightColor: 'transparent',
                borderWidth: 1,

                _txt: {
                    fontWeight: '400',
                    fontSize: 10,
                    marginTop: 5,
                    marginBottom: 10,
                    width: '95%'
                },
                _floating_txt: {
                    fontWeight: '600',
                    fontSize: 12,
                    color: 'gray'
                },
                _icon: {
                    height: 18,
                    width: 18,
                    backgroundColor: '#fff',
                    position: 'absolute',
                    top: 7,
                    right: 0
                }
            }
        }
    },

    _gift_view: {
        width: '90%',

        _renderView: {
            width: '50%',
            backgroundColor: '#fff',
            alignItems: 'center',
            padding: 10,

            _view: {
                backgroundColor: '#fff3f3',
                width: '100%',
                height: 150,
                alignItems: 'center',
                position: 'relative',
                borderRadius: 10,

                _img: {
                    resizeMode: 'cover',
                    height: 70,
                    width: 70,
                    marginTop: 25
                },
                _point_txt_view: {
                    width: '100%',
                    height: 30,
                    backgroundColor: '#ee1d23',
                    position: 'absolute',
                    bottom: 0,
                    borderRadius: 10,
                    justifyContent: 'center',
                    alignItems: 'center',

                    _txt: {
                        color: '#fff',
                        fontWeight: '600'
                    }
                }
            }
        }
    }

})
