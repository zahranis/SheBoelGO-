package com.example.finalproject

import android.annotation.SuppressLint
import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Scaffold
import androidx.compose.ui.Modifier
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.finalproject.ui.*
import com.example.finalproject.ui.theme.FinalProjectTheme

class MainActivity : ComponentActivity() {
    @SuppressLint("UnusedMaterial3ScaffoldPaddingParameter")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        setContent {
            FinalProjectTheme {
                val navController = rememberNavController()

                NavHost(
                    navController = navController,
                    startDestination = "main"
                ) {
                    composable("landing") {
                        LandingScreen(navController = navController)
                    }
                    composable("login") {
                        AuthScreen(
                            onLoginSuccess = { navController.navigate("main") },
                            onBack = { navController.popBackStack() }
                        )
                    }
                    composable("register") {
                        AuthScreen(
                            onLoginSuccess = { navController.navigate("main") },
                            onBack = { navController.popBackStack() }
                        )
                    }
                    composable("main") {
                        Scaffold(
                            bottomBar = { BottomNavigationBar(selectedItem = 0, onItemSelected = {}) }
                        ) { innerPadding ->
                            Box(modifier = Modifier.padding(innerPadding)) {
                                HomeScreen()
                            }
                        }
                    }
                }
            }
        }
    }
}
