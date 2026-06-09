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
    _user_option:
    {
        flexDirection: 'row',
        justifyContent: 'space-between',
        width: '100%',
        // backgroundColor:'pink',
        height: 35,
        marginTop: '2%',

        _view:
        {
            flex: 1,
            alignItems: 'center',

            _img:
            {
                height: 20,
                width: 20
            },
            _txt:
            {
                color: '#fff',
                fontSize: 11
            }
        }
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
        justifyContent: 'center',
        alignItems: 'center',
        overflow: 'hidden',


        _view: {
            width: '100%',
            alignItems: 'center',
            marginTop: 0,
            overflow: 'hidden'

        },

        _card: {
            height: '100%',
            width: '90%',
            // backgroundColor: '#fff',
            // alignItems: 'center',
            // borderRadius: 5,
            // borderColor: 'gray',
            // borderWidth: 1,
            padding: 10
        },

        _triangle: {
            marginTop: -30,
            width: 0,
            height: 0,
            backgroundColor: 'transparent',
            borderStyle: 'solid',
            borderTopWidth: 0,
            borderRightWidth: 10,
            borderBottomWidth: 20,
            borderLeftWidth: 10,
            borderTopColor: 'transparent',
            borderRightColor: 'transparent',
            borderBottomColor: '#fff',
            borderLeftColor: 'transparent',
        },

        _notification_section: {
            borderBottomColor: 'gray',
            borderTopColor: 'transparent',
            borderLeftColor: 'transparent',
            borderRightColor: 'transparent',
            width: '100%', marginTop: 10, padding: 10,

            _contact: {
                fontWeight: '600',
                fontSize: 14,
                color: '#000'
            },
            _content: {
                fontWeight: '400',
                fontSize: 12,
                marginTop: 5
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