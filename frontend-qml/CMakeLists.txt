cmake_minimum_required(VERSION 3.22)
project(taikobot VERSION 1.0 LANGUAGES CXX)

set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

find_package(Qt6 REQUIRED COMPONENTS
    Core
    Quick
    Network
    Sql
    QuickControls2
)

qt_standard_project_setup(REQUIRES 6.5)

# Bake API base URL and OAuth client credentials at build time
configure_file(src/AppConfig.h.in src/AppConfig.h @ONLY)

set(QML_FILES
    qml/main.qml
    qml/TaikoBotApp.qml
    qml/navigation/AppNavigator.qml
    qml/pages/LoginPage.qml
    qml/pages/DashboardPage.qml
    qml/pages/CourseIndexPage.qml
    qml/pages/CourseShowPage.qml
    qml/pages/CourseEditPage.qml
    qml/pages/LessonShowPage.qml
    qml/pages/LessonEditPage.qml
    qml/pages/UserIndexPage.qml
    qml/pages/UserEditPage.qml
    qml/pages/ProfilePage.qml
    qml/pages/ErrorPage.qml
    qml/components/TButton.qml
    qml/components/TCard.qml
    qml/components/TKarmaDisplay.qml
    qml/components/TParticipationBadge.qml
    qml/components/TOfflineBanner.qml
    qml/components/TPageHeader.qml
)

set(CPP_SOURCES
    src/main.cpp
    src/api/ApiClient.h
    src/api/ApiClient.cpp
    src/models/UserModel.h
    src/models/CourseModel.h
    src/models/LessonModel.h
    src/models/LessonParticipantModel.h
    src/models/Participation.h
    src/storage/LocalDatabase.h
    src/storage/LocalDatabase.cpp
    src/storage/CacheManager.h
    src/storage/CacheManager.cpp
    src/sync/PendingOpQueue.h
    src/sync/PendingOpQueue.cpp
    src/sync/SyncEngine.h
    src/sync/SyncEngine.cpp
    src/stores/AuthStore.h
    src/stores/AuthStore.cpp
    src/stores/CourseStore.h
    src/stores/CourseStore.cpp
    src/stores/LessonStore.h
    src/stores/LessonStore.cpp
    src/stores/UserStore.h
    src/stores/UserStore.cpp
)

qt_add_executable(taikobot ${CPP_SOURCES})

qt_add_qml_module(taikobot
    URI TaikoBot
    VERSION 1.0
    QML_FILES ${QML_FILES}
)

target_include_directories(taikobot PRIVATE ${CMAKE_CURRENT_BINARY_DIR}/src)

target_link_libraries(taikobot PRIVATE
    Qt6::Core
    Qt6::Quick
    Qt6::Network
    Qt6::Sql
    Qt6::QuickControls2
)

# WASM-specific settings
if(EMSCRIPTEN)
    set_target_properties(taikobot PROPERTIES
        QT_WASM_PTHREAD_POOL_SIZE 4
    )
endif()
