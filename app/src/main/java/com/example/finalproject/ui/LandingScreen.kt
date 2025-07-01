package com.example.finalproject.ui

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavController
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import com.example.finalproject.ui.theme.HennyPenny

@Composable
fun LandingScreen(
    navController: NavController,
    appName1: String = "She",
    appName2: String = "Boel",
    appName3: String = "GO!",
) {
    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF6DA0FF))
            .padding(24.dp)
    ) {
        Column {
            Spacer(modifier = Modifier.height(100.dp))

            Text(
                text = appName1,
                fontSize = 72.sp,
                fontWeight = FontWeight.Bold,
                fontFamily = HennyPenny,
                color = Color.White,
            )
            Text(
                text = appName2,
                fontSize = 72.sp,
                fontWeight = FontWeight.Bold,
                fontFamily = HennyPenny,
                color = Color.White,
            )
            Text(
                text = appName3,
                fontSize = 72.sp,
                fontWeight = FontWeight.Bold,
                fontFamily = HennyPenny,
                color = Color.White,
            )
        }

        Column(
            modifier = Modifier
                .fillMaxWidth()
                .align(Alignment.BottomCenter),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            OutlinedButton(
                onClick = { navController.navigate("register") },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(56.dp)
                    .padding(vertical = 8.dp),
                colors = ButtonDefaults.outlinedButtonColors(
                    containerColor = Color.Transparent,
                    contentColor = Color.White
                ),
                border = BorderStroke(1.dp, Color.White)
            ) {
                Text("Register")
            }

            Button(
                onClick = { navController.navigate("login") },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(56.dp)
                    .padding(vertical = 8.dp),
                colors = ButtonDefaults.buttonColors(
                    containerColor = Color.White,
                    contentColor = Color(0xFF6DA0FF)
                )
            ) {
                Text("Login")
            }
        }
    }
}